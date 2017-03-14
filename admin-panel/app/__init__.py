from flask import Flask, url_for, redirect, request, abort, render_template
from flask_sqlalchemy import SQLAlchemy
from flask_admin import Admin
from flask_admin.contrib.sqla import ModelView
import flask_login

app = Flask(__name__)
app.config.from_object('config')
db = SQLAlchemy(app)
from .models import User, Role
from .views import AdminIndexView, AdminUserView

# Initialize flask-login
def init_login():
    login_manager = flask_login.LoginManager()
    login_manager.init_app(app)

    # Create user loader function
    @login_manager.user_loader
    def load_user(user_id):
        return db.session.query(User).get(user_id)
init_login()

# Create Flask-Admin views
admin = Admin(
    app,
    'Admin Panel',
    index_view=AdminIndexView(),
    base_template='master.html',
    template_mode='bootstrap3',
)
admin.add_view(AdminUserView(User, db.session))
admin.add_view(ModelView(Role, db.session))