from werkzeug.security import safe_str_cmp
from models.user import UserModel



def authenticate(username, password):
    user = UserModel.find_by_username(username)
    if user and safe_str_cmp(user.password, password):
        return user

#if user authenticates return user object in json payload.
def identity(payload):
    user_id	= payload['identity']
    return Usermodel.find_by_id(user_id)