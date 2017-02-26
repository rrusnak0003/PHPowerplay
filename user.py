import sqlite3 #temporary for testing purposes

from flask_restful import Resource, reqparse

class User: #user class is a class object with username, password and email fields.
    def __init__(self, _id, username, password, email):
        self.id = _id
        self.username = username
        self.password = password
        self.email = email

        @classmethod
        def find_by_username(cls, username): #find user in db by username
            connection = sqlite3.connect('data.db')
            cursor = connection.cursor()

            query = "SELECT * FROM users WHERE username=?"

            result = cursor.execute(query, (username,))
            row = result.fetchone()
            if row:
                user = cls(*row)

            else:
                user = None

            connection.close()
            return user

        @classmethod
        def find_by_id(cls, _id): #find user in db by id
            connection = sqlite3.connect('data.db')
            cursor = connection.cursor()

            query = "SELECT * FROM users WHERE id=?"

            result = cursor.execute(query, (_id,))
            row = result.fetchone()
            if row:
                user = cls(*row)

            else:
                user = None

            connection.close()
            return user