from app import db
from werkzeug.security import generate_password_hash


class User(db.Model):
    user_id = db.Column(db.Integer, primary_key=True)
    username = db.Column(db.String(100), unique=True)
    password = db.Column(db.String(100))
    email = db.Column(db.String(100), unique=True)
    approved = db.Column(db.Boolean, default=False)
    active = db.Column(db.Boolean, default=True)
    admin = db.Column(db.Boolean, default=False)
    # Role relationship. Supposed that one user has only one role, and one role can be given to multiple users
    role_id = db.Column(db.Integer, db.ForeignKey('role.role_id'))

    def __str__(self):
        return self.username

    def is_authenticated(self):
        return True

    def is_active(self):
        return self.active

    def is_anonymous(self):
        return False

    def get_id(self):
        return self.user_id

    def is_admin(self):
        return self.admin

    def set_password(self, password):
        self.password = generate_password_hash(password)

class Role(db.Model):
    role_id = db.Column(db.Integer, primary_key=True)
    role_name = db.Column(db.String(100), unique=True)
    #permissions = db.Column(db.Integer) #I'm not sure that it's needed but in requirements it's said "The permissions field will be stored as an integer."
    user = db.relationship('User', backref='role', lazy='dynamic')

    def __str__(self):
        return self.role_name