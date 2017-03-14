from flask import url_for, redirect, request, flash
from flask_admin.actions import action
import flask_admin as admin
import flask_login
from flask_admin.contrib.sqla import ModelView
from wtforms import form, fields, validators
from werkzeug.security import generate_password_hash, check_password_hash
from flask_admin import helpers, expose
from wtforms import TextField
from app import db
from .models import User


class LoginForm(form.Form):
    username = fields.StringField(validators=[validators.required()])
    password = fields.PasswordField(validators=[validators.required()])

    def validate_login(self):
        user = self.get_user()
        if user is None or user.admin == False:
            raise validators.ValidationError('Invalid user or you don\'t have enough permissions')

        # compare hashed password in database with user input
        if not check_password_hash(user.password, self.password.data):
            raise validators.ValidationError('Invalid password')

    def get_user(self):
        return db.session.query(User).filter_by(username=self.username.data).first()

class PasswordFiled(TextField):
    def process_data(self, value):
        self.data = ''  # even if password is already set, don't show hash here or else it will be double-hashed on save
        self.orig_hash = value
    def process_formdata(self, valuelist):
        value = ''
        if valuelist:
            value = valuelist[0]
        if value:
            self.data = generate_password_hash(value)
        else:
            self.data = self.orig_hash

# Create customized model view class
class AdminUserView(ModelView):
    form_overrides = dict(
        password=PasswordFiled,
    )
    form_widget_args = dict(
        password=dict(
            placeholder='Enter new password here to change password',
        ),
    )
    def is_accessible(self):
        return flask_login.current_user.is_authenticated

    form_excluded_columns = ('password')

    column_exclude_list = ('password')

    # Create approve action
    @action('approve', 'Approve', 'Are you sure you want to approve selected users?')
    def action_approve(self, ids):
        try:
            query = User.query.filter(User.user_id.in_(ids))
            for user in query.all():
                user.approved = True
            db.session.commit()
            flash('Users were successfully approved')
        except Exception as ex:
            error = str(ex)
            flash('Failed to approve users. %(error)s') % error

    # Create reset password action to default dominion password
    @action('reset password', 'Reset password', 'Are you sure you want to reset password selected users?')
    def action_reset_password(self, ids):
        try:
            query = User.query.filter(User.user_id.in_(ids))
            for user in query.all():
                user.password = generate_password_hash('dominion')
            db.session.commit()
            flash('Passwords were successfully reseted')
        except Exception as ex:
            error = str(ex)
            flash('Failed to reset password. %(error)s') % error

    # Create activate action
    @action('activate', 'Activate', 'Are you sure you want to activate selected user accounts?')
    def action_activate_user(self, ids):
        try:
            query = User.query.filter(User.user_id.in_(ids))
            for user in query.all():
                user.active = True
            db.session.commit()
            flash('Accounts were successfully activated')
        except Exception as ex:
            error = str(ex)
            flash('Failed to activate user accounts. %(error)s') % error

    # Create deactivate action
    @action('deactivate', 'Deactivate', 'Are you sure you want to deactivate selected user accounts?')
    def action_deactivate_user(self, ids):
        try:
            query = User.query.filter(User.user_id.in_(ids))
            for user in query.all():
                user.active = False
            db.session.commit()
            flash('Accounts were successfully deactivated')
        except Exception as ex:
            error = str(ex)
            flash('Failed to deactivate user accounts. %(error)s') % error

# Create customized index view class that handles login
class AdminIndexView(admin.AdminIndexView):
    @expose('/')
    def index(self):
        if not flask_login.current_user.is_authenticated:
            return redirect(url_for('.login_view'))
        return super(AdminIndexView, self).index()

    @expose('/login/', methods=('GET', 'POST'))
    def login_view(self):
        # handle user login
        form = LoginForm(request.form)
        if helpers.validate_form_on_submit(form):
            user = form.get_user()
            form.validate_login()
            flask_login.login_user(user)

        if flask_login.current_user.is_authenticated:
            return redirect(url_for('.index'))
        self._template_args['form'] = form
        return super(AdminIndexView, self).index()

    @expose('/logout/')
    def logout_view(self):
        flask_login.logout_user()
        return redirect(url_for('.index'))