from functools import wraps, update_wrapper

from flask import current_app
from flask import redirect
from flask import session, url_for, request
from flask.ext.login import current_user



def requires_roles(*roles):
    def wrapper(f):
        @wraps(f)
        def wrapped(*args, **kwargs):
            if current_user.permission not in roles:
                return "Unauthorized"
            return f(*args, **kwargs)

        return wrapped

    return wrapper

# def login_required(role="ANY"):
#     def wrapper(fn):
#         @wraps(fn)
#         def decorated_view(*args, **kwargs):
#
#             if not current_user.is_authenticated():
#                return current_app.login_manager.unauthorized()
#             urole = current_app.login_manager.reload_user().get_urole()
#             if ( (urole != role) and (role != "ANY")):
#                 return current_app.login_manager.unauthorized()
#             return fn(*args, **kwargs)
#         return decorated_view
#     return wrapper