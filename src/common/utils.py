import re

from passlib.hash import pbkdf2_sha512


class Utils(object):
    def hash_password(password):
        """
        Hashed a password using pbkdf2_sha512
        :param password: The sha512 passwrord fromt he login/register form
        :return: A sha512->  pbkdf2_sha512 encrypted password
        """
        return pbkdf2_sha512.encrypt(password)

    @staticmethod
    def check_hashed_password(password, hashed_password):
        """
        Checks that the password the user sent matches that of the database.
        The database password is encrypted more than the user's password at this stage.
        :param password: sha512-hashed password
        :param hashed_password: pbkdf2_sha512
        :return: True if password match, False otherwise
        """

        return pbkdf2_sha512.verify(password, hashed_password)

    @staticmethod
    def email_is_valid(email):
        email_address_matcher = re.compile('^[\w-]+@([\w-]+\.)+[\w]+$')
        return True if email_address_matcher.match(email) else False