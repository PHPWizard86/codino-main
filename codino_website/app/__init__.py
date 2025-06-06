from flask import Flask
from config import Config
from flask_sqlalchemy import SQLAlchemy
from flask_login import LoginManager
from flask_migrate import Migrate

db = SQLAlchemy()
login_manager = LoginManager()
login_manager.login_view = 'auth.login' # Redirect to login page if user not authenticated
migrate = Migrate()

def create_app(config_class=Config):
    app = Flask(__name__, instance_relative_config=True)
    app.config.from_object(config_class)
    app.config.from_pyfile('config.py', silent=True) # For instance folder config

    db.init_app(app)
    login_manager.init_app(app)
    migrate.init_app(app, db)

    from app.main import bp as main_bp
    app.register_blueprint(main_bp)

    from app.auth import bp as auth_bp
    app.register_blueprint(auth_bp, url_prefix='/auth')

    from app.dashboard import bp as dashboard_bp
    app.register_blueprint(dashboard_bp)

    # We will add other blueprints later (e.g., for dashboard, admin)

    with app.app_context():
        # You might want to create tables if they don't exist,
        # but Flask-Migrate is generally preferred for schema management.
        # db.create_all() # Be cautious with this in production or with migrations

        # Pre-populate TicketStatus if it's empty
        from app.models.ticket import TicketStatus # Import here to avoid circular dependency issues at top level
        if db.session.query(TicketStatus.id).first() is None:
            statuses = ['Open', 'Under Review', 'Resolved', 'Needs More Info', 'Closed']
            for status_name in statuses:
                status = TicketStatus(name=status_name)
                db.session.add(status)
            db.session.commit()
            # print('Ticket statuses populated.') # Avoid print in production app factory

        # Pre-populate Plan if it's empty
        from app.models.plan import Plan # Import here
        if db.session.query(Plan.id).first() is None:
            plans_data = [
                {'name': 'Free', 'ticket_limit': 1, 'price': 0.0, 'features': '1 ticket/month'},
                {'name': 'Standard', 'ticket_limit': 5, 'price': 10.0, 'features': '5 tickets/month + faster response'},
                {'name': 'Pro', 'ticket_limit': None, 'price': 25.0, 'features': 'Unlimited tickets + priority support'} # None for unlimited
            ]
            for p_data in plans_data:
                plan_obj = Plan(name=p_data['name'], ticket_limit=p_data['ticket_limit'], price=p_data['price'], features=p_data['features'])
                db.session.add(plan_obj)
            db.session.commit()
            # print('Subscription plans populated.')

    return app

# Import models here to ensure they are known to SQLAlchemy before db.create_all() or migrations
from app.models import user, ticket, website, plan
