from flask import Flask, request
from flask_restful import Resource, Api, reqparse
from flask_jwt import JWT, jwt_required
from security import authenticate, identity
from user import UserRegister

#app.py creates application for json endpoints for use with front end.

app = Flask(__name__)
app.secret_key = 'addy'
api = Api(app)

jwt = JWT(app, authenticate, identity) # /auth





api.add_resource(UserRegister, '/register') # register endpoint

app.run(port=5000, debug=True)