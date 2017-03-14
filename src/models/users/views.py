from flask import Blueprint
from flask import redirect
from flask import render_template
from flask import request
from flask import session
from flask import url_for
import src.models.users.errors as UserErrors
import src.models.users.decorators as user_decorators


from src.models.users.user import User

user_blueprint = Blueprint('users', __name__)

@user_blueprint.route('/login', methods=['GET', 'POST'])
def login_user():
    if request.method == 'POST':
        username = request.form['username']
        password = request.form['password']
        try:
            if User.is_login_valid(username, password):
                session['email'] = username
                return redirect(url_for('home'))
        except UserErrors.UserError as e:
            return e.message

    return render_template("users/login.jinja2")


@user_blueprint.route('/register', methods=['GET', 'POST'])
def register_user():
    if request.method == 'POST':
        username = request.form['username']
        email = request.form['email']
        password = request.form['password']
        active = False
        permission=1
        try:
            if User.register_user(username,email, password,active,permission):
                #session['email'] = email
                return redirect(url_for('home'))
        except UserErrors.UserError as e:
            return e.message

    return render_template("users/register.jinja2")



@user_blueprint.route('/alerts')
@user_decorators.requires_login
def user_alerts():
    user = User.find_by_email(session['email'])
    alerts = user.get_alerts()
    return render_template('users/alerts.jinja2', alerts=alerts)


@user_blueprint.route('/logout')
def logout_user():
    session['email'] = None
    return redirect(url_for('home'))



@user_blueprint.route('/admin')
@user_decorators.requires_login
def admin():
    users = User.find_all()

    return render_template('users/admin.jinja2', users=users)



@user_blueprint.route('/get_user_page/<string:user_id>')
@user_decorators.requires_login
def get_user_page(user_id):
    user = User.find_by_id(user_id)
    return render_template('users/edit_users.jinja2', user=user)


@user_blueprint.route('/deactivate/<string:user_id>')
@user_decorators.requires_login
def deactivate_user(user_id):
    user = User.find_by_id(user_id)
    user.deactivate()
    return render_template('users/edit_users.jinja2', user=user)

@user_blueprint.route('/activate/<string:user_id>')
@user_decorators.requires_login
def activate_user(user_id):
    user = User.find_by_id(user_id)
    user.activate()
    return render_template('users/edit_users.jinja2', user=user)

@user_blueprint.route('/delete/<string:user_id>')
@user_decorators.requires_login
def delete_user(user_id):
    User.find_by_id(user_id).delete()
    return redirect(url_for('users.admin'))



@user_blueprint.route('/create', methods=['GET', 'POST'])
@user_decorators.requires_login
def create_user():
    if request.method == 'POST':
        email = request.form['email']
        password = request.form['password']
        username = request.form['username']
        permission = int(request.form['permission'])
        active = request.form['active']

        try:
            if User.register_user(username,email, password, active, permission):
                return redirect(url_for(".admin"))
        except UserErrors.UserError as e:
            return e.message

    return render_template("users/create_user.jinja2")




@user_blueprint.route('/check_alerts/<string:user_id>')
def check_user_alerts(user_id):
    pass
