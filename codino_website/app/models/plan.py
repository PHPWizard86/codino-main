from app import db

class Plan(db.Model):
    id = db.Column(db.Integer, primary_key=True)
    name = db.Column(db.String(50), unique=True, nullable=False) # Free, Standard, Pro
    ticket_limit = db.Column(db.Integer) # Null for unlimited
    price = db.Column(db.Float) # Monthly price
    features = db.Column(db.Text) # Description of plan features, e.g., "Faster response"

    users = db.relationship('User', backref='plan', lazy='dynamic')

    def __repr__(self):
        return f'<Plan {self.name}>'
