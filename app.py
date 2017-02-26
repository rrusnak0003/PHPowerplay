from flask import Flask, request, render_template
from flask_restful import Resource, Api, reqparse
from flask_jwt import JWT, jwt_required
from security import authenticate, identity
from user import UserRegister

#app.py creates application for json endpoints for use with front end.

app = Flask(__name__)
app.secret_key = 'addy'
api = Api(app)

jwt = JWT(app, authenticate, identity) # /auth

@app.route('/')
def home():
    return render_template('home.html')


@app.route('/about/')
def about():
    return render_template('about.html')


@app.route('/register/')
def register():

    return render_template('register.html')






api.add_resource(UserRegister, '/registration') # register endpoint

app.run(port=5000, debug=True)