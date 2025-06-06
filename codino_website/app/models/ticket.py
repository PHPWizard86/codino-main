from app import db
from datetime import datetime

class TicketStatus(db.Model): # To manage ticket statuses
    id = db.Column(db.Integer, primary_key=True)
    name = db.Column(db.String(50), unique=True, nullable=False) # e.g., Open, Under Review, Resolved

    def __repr__(self):
        return f'<TicketStatus {self.name}>'

class Ticket(db.Model):
    id = db.Column(db.Integer, primary_key=True)
    title = db.Column(db.String(100), nullable=False)
    description = db.Column(db.Text, nullable=False)
    created_at = db.Column(db.DateTime, index=True, default=datetime.utcnow)
    updated_at = db.Column(db.DateTime, index=True, default=datetime.utcnow, onupdate=datetime.utcnow)
    user_id = db.Column(db.Integer, db.ForeignKey('user.id'), nullable=False) # User who submitted
    website_id = db.Column(db.Integer, db.ForeignKey('website.id'), nullable=False) # Registered website
    status_id = db.Column(db.Integer, db.ForeignKey('ticket_status.id')) # e.g., "Under Review"

    # Relationships
    status = db.relationship('TicketStatus', backref='tickets')
    # attachments = db.relationship('TicketAttachment', backref='ticket', lazy='dynamic') # For file uploads
    # communications = db.relationship('TicketCommunication', backref='ticket', lazy='dynamic') # For messages

    def __repr__(self):
        return f'<Ticket {self.title}>'

# We can add TicketAttachment and TicketCommunication models later
