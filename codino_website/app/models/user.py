from app import db, login_manager
from flask_login import UserMixin
# from werkzeug.security import generate_password_hash, check_password_hash

@login_manager.user_loader
def load_user(user_id):
    return User.query.get(int(user_id))

class User(UserMixin, db.Model):
    id = db.Column(db.Integer, primary_key=True)
    email = db.Column(db.String(120), index=True, unique=True, nullable=False)
    phone_number = db.Column(db.String(20), index=True, unique=True, nullable=True) # Nullable for now
    password_hash = db.Column(db.String(256)) # Increased length for stronger hashes
    name = db.Column(db.String(64), nullable=True)
    email_verified = db.Column(db.Boolean, default=False)
    phone_verified = db.Column(db.Boolean, default=False)
    plan_id = db.Column(db.Integer, db.ForeignKey('plan.id')) # Foreign key to Plan model

    # Relationships
    websites = db.relationship('Website', backref='owner', lazy='dynamic')
    tickets = db.relationship('Ticket', backref='requester', lazy='dynamic') # Tickets submitted by user

    # def set_password(self, password):
    #     self.password_hash = generate_password_hash(password)

    # def check_password(self, password):
    #     return check_password_hash(self.password_hash, password)

    def __repr__(self):
        return f'<User {self.email}>'
