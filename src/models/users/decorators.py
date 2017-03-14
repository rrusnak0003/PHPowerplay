from functools import wraps, update_wrapper

from flask import redirect
from flask import session, url_for, request


def requires_login(func):
    @wraps(func)
    def decorated_function(*args, **kwargs):
        if 'email' not in session.keys() or session['email'] is None:
            return redirect(url_for('users.login_user', next=request.path))
        return func(*args, **kwargs)
    return decorated_function

def requires_admin(func):
    @wraps(func)
    def decorated_function(*args, **kwargs):
        if 'email' not in session.keys() or session['email'] is None:
            return redirect(url_for('home', next=request.path))
        return func(*args, **kwargs)
    return decorated_function

def requires_analyst(func):
    @wraps(func)
    def decorated_function(*args, **kwargs):
        if 'email' not in session.keys() or session['email'] is None:
            return redirect(url_for('home', next=request.path))
        return func(*args, **kwargs)
    return decorated_function

