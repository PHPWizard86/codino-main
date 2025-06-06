from flask_wtf import FlaskForm
from wtforms import StringField, PasswordField, SubmitField
from wtforms.validators import DataRequired, Email, EqualTo, Length, Optional, Regexp
from flask_login import current_user
# from app.models.user import User # Import User for validation if needed directly in form
# Imports for TicketSubmissionForm (as per the script)
from wtforms import SelectField, TextAreaField
# from flask_wtf.file import FileField, FileAllowed, FileRequired # For file uploads (optional for now)
# from app.models.website import Website # To populate website choices - usually done in route

class ProfileForm(FlaskForm):
    name = StringField('Name', validators=[Optional(), Length(max=64)])
    email = StringField('Email', validators=[DataRequired(), Email(), Length(max=120)])
    phone_number = StringField('Mobile Number', validators=[Optional(), Length(min=10, max=20), Regexp(r'^[0-9+()-]*$', message="Invalid characters in phone number.")])
    current_password = PasswordField('Current Password (only if changing password)')
    new_password = PasswordField('New Password (leave blank to keep current)', validators=[Optional(), Length(min=8)])
    confirm_new_password = PasswordField('Confirm New Password', validators=[EqualTo('new_password', message='New passwords must match.')])
    submit = SubmitField('Update Profile')

    # def validate_email(self, email):
    #     if email.data != current_user.email:
    #         user = User.query.filter_by(email=email.data).first()
    #         if user:
    #             raise ValidationError('That email is already taken. Please choose a different one.')

    # def validate_phone_number(self, phone_number):
    #     if phone_number.data and phone_number.data != current_user.phone_number:
    #         user = User.query.filter_by(phone_number=phone_number.data).first()
    #         if user:
    #             raise ValidationError('That phone number is already taken. Please choose a different one.')

class WebsiteRegistrationForm(FlaskForm):
    domain_name = StringField('Domain Name (e.g., example.com)', validators=[DataRequired(), Length(max=255)])
    website_type = StringField('Website Type (e.g., Blog, eCommerce, Corporate)', validators=[Optional(), Length(max=50)])
    description = StringField('Description or Notes', validators=[Optional(), Length(max=500)]) # Using StringField for shorter text, could be TextAreaField for longer
    submit = SubmitField('Register Website')

class TicketSubmissionForm(FlaskForm):
    website_id = SelectField('Select Registered Website', coerce=int, validators=[DataRequired()])
    title = StringField('Issue Title (e.g., "Broken download link")', validators=[DataRequired(), Length(max=100)])
    description = TextAreaField('Detailed Description of the Problem', validators=[DataRequired(), Length(max=5000)])
    # attachments = FileField('Upload Screenshots/Files (Optional)', validators=[
    #     Optional(),
    #     FileAllowed(['jpg', 'jpeg', 'png', 'gif', 'pdf', 'txt', 'zip'], 'Allowed file types: Images, PDF, TXT, ZIP')
    # ]) # We will implement file handling logic later
    submit = SubmitField('Submit Ticket')

    def __init__(self, *args, **kwargs):
        super(TicketSubmissionForm, self).__init__(*args, **kwargs)
        # Populate website choices for the current user
        # This requires current_user to be available, typically handled in the route
        # For now, this means the route will need to populate choices.
        # self.website_id.choices = [(w.id, w.domain_name) for w in Website.query.filter_by(user_id=current_user.id).all()]
