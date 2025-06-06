from flask import Blueprint

bp = Blueprint('dashboard', __name__, template_folder='templates', url_prefix='/dashboard')

from app.dashboard import routes #, forms # forms can be imported in routes where needed
