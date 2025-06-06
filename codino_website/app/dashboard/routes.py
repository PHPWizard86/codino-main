import datetime # Added for plan limit check
from sqlalchemy import extract # Added for plan limit check
from flask import render_template, redirect, url_for, flash, request
from flask_login import login_required, current_user
from app import db
from app.dashboard import bp
from app.dashboard.forms import ProfileForm # Keep this specific
from app.models.user import User # For validation checks
from wtforms.validators import ValidationError # For custom form validation feedback
# Imports for website management
from app.dashboard.forms import WebsiteRegistrationForm
from app.models.website import Website
# Imports for ticket management
from app.dashboard.forms import TicketSubmissionForm
from app.models.ticket import Ticket, TicketStatus


@bp.route('/')
@login_required
def index():
    # Main dashboard page, can show overview later
    return render_template('dashboard/index.html', title='Dashboard')

@bp.route('/profile', methods=['GET', 'POST'])
@login_required
def profile():
    form = ProfileForm(obj=current_user) # Pre-populate form with current user data

    if form.validate_on_submit():
        # Validate unique fields (email, phone) if changed
        if form.email.data != current_user.email:
            if User.query.filter_by(email=form.email.data).first():
                flash('That email is already taken. Please choose a different one.', 'danger')
                return render_template('dashboard/profile.html', title='Profile', form=form)

        if form.phone_number.data and form.phone_number.data != current_user.phone_number:
            if User.query.filter_by(phone_number=form.phone_number.data).first():
                flash('That phone number is already taken. Please choose a different one.', 'danger')
                return render_template('dashboard/profile.html', title='Profile', form=form)

        current_user.name = form.name.data
        current_user.email = form.email.data
        current_user.phone_number = form.phone_number.data

        if form.new_password.data:
            if not form.current_password.data : # Check if current_password field is empty
                flash('Current password is required to set a new password.', 'danger')
                return render_template('dashboard/profile.html', title='Profile', form=form)
            if not current_user.check_password(form.current_password.data):
                flash('Incorrect current password.', 'danger')
                return render_template('dashboard/profile.html', title='Profile', form=form)
            if form.new_password.data == form.current_password.data:
                flash('New password cannot be the same as the current password.', 'warning')
                return render_template('dashboard/profile.html', title='Profile', form=form)
            current_user.set_password(form.new_password.data)
            flash('Your password has been updated.', 'success')

        db.session.commit()
        flash('Your profile has been updated successfully!', 'success')
        return redirect(url_for('dashboard.profile'))
    elif request.method == 'GET':
        # Populate form with current_user data if not submitting (already done by obj=current_user)
        pass

    # Determine plan display name
    plan_display_name = "N/A (Plan system not fully implemented)"
    if current_user.plan:
        plan_display_name = current_user.plan.name
    elif hasattr(current_user, 'plan_id') and current_user.plan_id is None: # Check if plan_id exists and is None
        plan_display_name = "Free Plan (Default)" # Or some other default

    return render_template('dashboard/profile.html', title='Profile', form=form, plan_name=plan_display_name)

# Routes for website management
@bp.route('/websites', methods=['GET', 'POST'])
@login_required
def manage_websites():
    form = WebsiteRegistrationForm()
    if form.validate_on_submit():
        # Check if user already registered this domain
        existing_website = Website.query.filter_by(user_id=current_user.id, domain_name=form.domain_name.data).first()
        if existing_website:
            flash('You have already registered this domain name.', 'warning')
        else:
            new_website = Website(
                domain_name=form.domain_name.data,
                website_type=form.website_type.data,
                description=form.description.data,
                owner=current_user # This links the website to the current user
            )
            db.session.add(new_website)
            db.session.commit()
            flash(f'Website {new_website.domain_name} registered successfully!', 'success')
            return redirect(url_for('dashboard.manage_websites'))

    user_websites = Website.query.filter_by(user_id=current_user.id).order_by(Website.registration_date.desc()).all()
    return render_template('dashboard/manage_websites.html', title='Manage Websites', form=form, websites=user_websites)

# Optional: Route for editing/deleting a website (can be added later if complex)
# @bp.route('/websites/edit/<int:website_id>', methods=['GET', 'POST'])
# @login_required
# def edit_website(website_id):
#     website = Website.query.get_or_404(website_id)
#     if website.owner != current_user:
#         flash('You do not have permission to edit this website.', 'danger')
#         return redirect(url_for('dashboard.manage_websites'))
#     # ... form logic for editing ...
#     pass

# @bp.route('/websites/delete/<int:website_id>', methods=['POST']) # Should be POST for deletion
# @login_required
# def delete_website(website_id):
#     website = Website.query.get_or_404(website_id)
#     if website.owner != current_user:
#         flash('You do not have permission to delete this website.', 'danger')
#         return redirect(url_for('dashboard.manage_websites'))
#     # db.session.delete(website)
#     # db.session.commit()
#     # flash('Website deleted successfully.', 'success')
#     pass

# Routes for ticket management
@bp.route('/tickets', methods=['GET', 'POST'])
@login_required
def manage_tickets():
    form = TicketSubmissionForm()
    # Populate website choices for the current user
    form.website_id.choices = [(w.id, w.domain_name) for w in Website.query.filter_by(user_id=current_user.id).order_by(Website.domain_name).all()]

    if not form.website_id.choices:
        flash('You must register a website before you can submit a support ticket.', 'warning')
        # If there are no websites, don't try to validate the form on POST, or it will fail on website_id
        if request.method == 'POST':
             return redirect(url_for('dashboard.manage_websites'))

    # Only attempt to validate if there are website choices, or if it's a GET request (to show the warning)
    if form.website_id.choices and form.validate_on_submit():
        # Subscription plan limit check
        user_plan = current_user.plan
        if user_plan and user_plan.ticket_limit is not None: # None means unlimited
            today = datetime.date.today()
            # Count tickets submitted by the user this month
            tickets_this_month = Ticket.query.filter(
                Ticket.user_id == current_user.id,
                extract('month', Ticket.created_at) == today.month,
                extract('year', Ticket.created_at) == today.year
            ).count()

            if tickets_this_month >= user_plan.ticket_limit:
                flash(f"You have reached your monthly ticket limit of {user_plan.ticket_limit} for the {user_plan.name} plan. Please upgrade for more tickets.", "warning")
                return redirect(url_for("dashboard.manage_tickets")) # Or to a plans/upgrade page

        # Double check in case of malicious POST (though choices are server-side populated)
        # This check might be redundant if form validation for SelectField already covers it well.
        # selected_website_id = form.website_id.data
        # if not any(selected_website_id == choice[0] for choice in form.website_id.choices):
        #    flash('Invalid website selection.', 'danger')
        #    return redirect(url_for('dashboard.manage_tickets'))


        # Get the default "Open" status
        open_status = TicketStatus.query.filter_by(name='Open').first()
        if not open_status:
            # This should ideally not happen if seeding works
            flash('Critical error: Ticket status "Open" not found. Please contact support.', 'danger')
            return redirect(url_for('dashboard.manage_tickets'))

        new_ticket = Ticket(
            title=form.title.data,
            description=form.description.data,
            user_id=current_user.id,
            website_id=form.website_id.data, # This should be valid due to choices population and validation
            status_id=open_status.id # Set initial status to "Open"
        )
        # File upload handling would go here
        # if form.attachments.data:
        #     # ... save file and link to ticket ...
        #     pass

        db.session.add(new_ticket)
        db.session.commit()
        flash('Support ticket submitted successfully!', 'success')
        return redirect(url_for('dashboard.manage_tickets'))

    user_tickets = Ticket.query.filter_by(user_id=current_user.id).order_by(Ticket.created_at.desc()).all()
    return render_template('dashboard/manage_tickets.html', title='Support Tickets', form=form, tickets=user_tickets)

@bp.route('/tickets/<int:ticket_id>')
@login_required
def view_ticket(ticket_id):
    ticket = Ticket.query.get_or_404(ticket_id)
    if ticket.requester != current_user: # Ensure user can only see their own tickets
        flash('You do not have permission to view this ticket.', 'danger')
        return redirect(url_for('dashboard.manage_tickets'))
    # Communication history will be added later
    return render_template('dashboard/view_ticket.html', title=f"Ticket: {ticket.title}", ticket=ticket)
