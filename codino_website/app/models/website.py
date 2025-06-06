from app import db
from datetime import datetime

class Website(db.Model):
    id = db.Column(db.Integer, primary_key=True)
    domain_name = db.Column(db.String(255), index=True, nullable=False)
    website_type = db.Column(db.String(50)) # e.g., blog, store, corporate
    description = db.Column(db.Text)
    registration_date = db.Column(db.DateTime, default=datetime.utcnow)
    user_id = db.Column(db.Integer, db.ForeignKey('user.id'), nullable=False)

    # Relationship
    tickets = db.relationship('Ticket', backref='website', lazy='dynamic')

    def __repr__(self):
        return f'<Website {self.domain_name}>'
