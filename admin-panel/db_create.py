from app import db
from app.models import User
from werkzeug.security import generate_password_hash

# Create database with default admin user
db.create_all()
user = User()
user.username = 'admin'
user.password = generate_password_hash('admin')
user.email = 'admin@example.com'
user.approved = True
user.active = True
user.admin = True
db.session.add(user)
db.session.commit()