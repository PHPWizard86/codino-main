from flask import Blueprint, render_template, redirect, url_for, flash, request
from flask_login import login_user, logout_user, current_user # Added login_user, logout_user, current_user
from app import db # Added db
from app.auth.forms import LoginForm, RegistrationForm
from app.models.user import User # Added User model
from app.models.plan import Plan # Added Plan model
from wtforms.validators import ValidationError # For custom validation messages

bp = Blueprint('auth', __name__, template_folder='templates')

@bp.route('/register', methods=['GET', 'POST'])
def register():
    if current_user.is_authenticated:
        return redirect(url_for('main.index'))
    form = RegistrationForm()
    if form.validate_on_submit():
        # Check for existing email
        existing_email = User.query.filter_by(email=form.email.data).first()
        if existing_email:
            flash('That email is already taken. Please choose a different one.', 'danger')
            return render_template('auth/register.html', title='Register', form=form)

        # Check for existing phone number if provided
        if form.phone_number.data:
            existing_phone = User.query.filter_by(phone_number=form.phone_number.data).first()
            if existing_phone:
                flash('That phone number is already taken. Please choose a different one.', 'danger')
                return render_template('auth/register.html', title='Register', form=form)

        user = User(email=form.email.data, phone_number=form.phone_number.data)
        user.set_password(form.password.data)

        # Assign default plan
        default_plan = Plan.query.filter_by(name='Free').first()
        if default_plan:
            user.plan_id = default_plan.id
        else:
            # This case should ideally not happen if seeding works
            flash('Critical error: Default plan not found. Registration cannot proceed without a plan.', 'danger')
            return render_template('auth/register.html', title='Register', form=form) # Prevent registration

        db.session.add(user)
        db.session.commit()
        flash('Congratulations, you are now a registered user! Please log in.', 'success')
        # We will add email verification step here later
        return redirect(url_for('auth.login'))
    return render_template('auth/register.html', title='Register', form=form)

@bp.route('/login', methods=['GET', 'POST'])
def login():
    if current_user.is_authenticated:
        return redirect(url_for('main.index'))
    form = LoginForm()
    if form.validate_on_submit():
        user = User.query.filter((User.email == form.email_or_phone.data) | (User.phone_number == form.email_or_phone.data)).first()
        if user is None or not user.check_password(form.password.data):
            flash('Invalid email/phone or password.', 'danger')
            return render_template('auth/login.html', title='Sign In', form=form)

        # For now, directly log in. Later, add check for email_verified.
        login_user(user, remember=form.remember_me.data)
        flash(f'Welcome back, {user.email}!', 'success')
        next_page = request.args.get('next')
        return redirect(next_page) if next_page else redirect(url_for('main.index'))
    return render_template('auth/login.html', title='Sign In', form=form)

@bp.route('/logout')
def logout():
    logout_user()
    flash('You have been logged out.', 'info')
    return redirect(url_for('main.index'))

# Placeholder for email verification route - to be implemented
@bp.route('/verify_email/<token>')
def verify_email(token):
    # Logic to verify token and update user.email_verified
    flash('Email verification functionality to be implemented.', 'info')
    return redirect(url_for('main.index'))

# Placeholder for SMS verification - to be implemented
@bp.route('/request_sms_verification', methods=['GET', 'POST'])
def request_sms_verification():
    flash('SMS verification functionality to be implemented.', 'info')
    return redirect(url_for('main.index'))
