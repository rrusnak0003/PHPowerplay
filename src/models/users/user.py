import uuid

from src.common.database import Database
from src.common.utils import Utils
import src.models.users.errors as UserErrors
import src.models.users.constants as UserConstants
from src.models.alerts.alert import Alert


class User(object):
    def __init__(self, username, email, password, active, permission, _id=None):
        self.username = username
        self.email = email
        self.password = password
        self.permission = 3 if username is 'administrator' else permission
        self.active = True if username is 'administrator' else active
        self._id = uuid.uuid4().hex if _id is None else _id

    def __repr__(self):
        return "<User {}>".format(self.email)

    @staticmethod
    def is_login_valid(username, password):
        """
        This method verifies that an e-mail/password combo ( as sent by the site forms) is valid or not.
        checks that the e-mail exists, and that the password associated to that e-mail is correct.
        :param email: The user's email
        :param password: A sha512 hashed password
        :return: True if valid, False if otherwise
        """
        user_data = Database.find_one(UserConstants.COLLECTION, {"username": username}) #Password in sha512 - >pbkdf2_sha512
        if user_data is None:
            raise UserErrors.UserNotExistsError("Your user does not exist.")
        #if not user_data['active']:
            #raise UserErrors.UserDisabledError("Your user is disabled.")
        if not Utils.check_hashed_password(password, user_data['password']):
            raise UserErrors.IncorrectPasswordError("Your password was wrong.")


        return True

    @staticmethod
    def register_user(username, email, password, active, permission):
        """
        This method registers a user using e-mail and password.
        The password already comes hashed as a sha-512
        :param email: user's e-mail (might be invalid
        :param password: sha512-hsahed password
        :return: True if registered successfully, or False otherwise(exceptions can also be raised)
        """
        user_data = Database.find_one(UserConstants.COLLECTION, {"email": email})

        if user_data is not None:
            raise UserErrors.UserAlreadyRegisteredError("The e-mail you used to register already exists.")
        if not Utils.email_is_valid(email):
            raise UserErrors.InvalidEmailError("The e-mail does not have the right format.")
        user_data = Database.find_one(UserConstants.COLLECTION, {"username": username})
        if user_data is not None:
            raise UserErrors.UserAlreadyRegisteredError("The username you used to register already exists.")
        User(username,email, Utils.hash_password(password), active, permission).save_to_db()
        return True

    def save_to_db(self):
        Database.insert(UserConstants.COLLECTION, self.json())


    def json(self):
        return {
            "_id": self._id,
            "username":self.username,
            "email": self.email,
            "password": self.password,
            "active":self.active,
            "permission":self.permission
        }

    @classmethod
    def find_by_email(cls, email):
        return cls(**Database.find_one(UserConstants.COLLECTION,{'email': email}))

    @classmethod
    def find_by_username(cls, username):
        return cls(**Database.find_one(UserConstants.COLLECTION,{'username': username}))

    @classmethod
    def find_all(cls):
        return [cls(**elem) for elem in Database.find(UserConstants.COLLECTION,{})]

    def get_alerts(self):
        return Alert.find_by_user_email(self.email)

    @classmethod
    def find_by_id(cls, user_id):
        return cls(**Database.find_one(UserConstants.COLLECTION, {'_id': user_id}))

    def deactivate(self):
        self.active = False
        self.save_to_mongo()

    def activate(self):
        self.active = True
        self.save_to_mongo()

    def save_to_mongo(self):
        Database.update(UserConstants.COLLECTION, {"_id": self._id}, self.json())

    def delete(self):
        Database.remove(UserConstants.COLLECTION, {'_id': self._id})

    #def get_users(self):
    #    return User.find_all()
