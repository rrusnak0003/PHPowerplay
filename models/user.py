import sqlite3

from flask import session

from db import db


class UserModel(db.Model):
    __tablename__ = 'users'

    id = db.Column(db.Integer, primary_key=True)
    username = db.Column(db.String(80))
    password = db.Column(db.String(80))
    email = db.Column(db.String(80))

    def __init__(self, username, password, email):
        self.username = username
        self.password = password
        self.email = email

    def save_to_db(self):
        db.session.add(self)
        db.session.commit()

    def delete_from_db(self):
        db.session.delete(self)
        db.session.commit()

    @classmethod
    def find_by_username(cls, username):
        return cls.query.filter_by(username=username).first()  # SELECT * FROM items WHERE name = name LIMIT 1

    @classmethod
    def find_by_id(cls, _id):
        return cls.query.filter_by(id=_id).first()

    @classmethod
    def find_by_email(cls, email):
        return cls.query.filter_by(email=email).first()

    @staticmethod
    def login_valid(username, password):
        # User.Login_valid("a@odu.com","1234")
        # Check whether a user's email matches the password they sent us
        user = UserModel.find_by_username(username)
        if user is not None:
            return user.password == password
        return False

    @classmethod
    def register(cls, username, password, email):
        user = cls.find_by_email(email)
        if user is None:
            # if user doesnt exist we can create it
            new_user = cls(username, password, email)
            new_user.save_to_db()
            session['username'] = username
            return True
        else:
            return False

    @staticmethod
    def login(username):
        # Login_valid has already been called
        session['username'] = username
