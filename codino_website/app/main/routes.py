from flask import render_template, Blueprint, flash # Added flash
from flask_login import current_user # Added current_user
from app.models.plan import Plan # Import Plan model

bp = Blueprint('main', __name__, template_folder='templates') # Ensure template_folder is set if using blueprint specific folder

@bp.route('/')
@bp.route('/index')
def index():
    # Using a generic template for now, not 'main/index.html' to simplify base.html structure
    return render_template('index.html', title='Home')

# Public pages (placeholders from previous step, ensure they render a base template if needed)
@bp.route('/about')
def about():
    return render_template('simple_page.html', title='About Us', content='Content for About Us page.')

@bp.route('/contact')
def contact():
    # Add contact form later
    return render_template('simple_page.html', title='Contact Us', content='Content for Contact Us page.')

@bp.route('/terms')
def terms():
    return render_template('simple_page.html', title='Terms of Use', content='Content for Terms of Use page.')

@bp.route('/faq')
def faq():
    return render_template('simple_page.html', title='FAQ', content='Content for FAQ page.')

@bp.route('/plans')
def plans():
    all_plans = Plan.query.order_by(Plan.price).all()
    return render_template("plans.html", title="Subscription Plans", plans=all_plans)
