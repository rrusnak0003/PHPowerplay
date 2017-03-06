import os

from flask import Flask, request, render_template
from flask_restful import Api
from flask_jwt import JWT

from security import authenticate, identity
from resources.user import UserRegister
from resources.powerplant import Powerplant, PowerplantList
from resources.region import Region, RegionList


app = Flask(__name__)
app.config['SQLALCHEMY_DATABASE_URI'] = os.environ.get('DATABASE_URL', 'sqlite:///data.db')
app.config['SQLALCHEMY_TRACK_MODIFICATIONS'] = False
app.secret_key = 'addy'
api = Api(app)


#app.py creates application for json endpoints for use with front end.

@app.before_first_request
def create_tables():
    db.create_all()

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


api.add_resource(Region, '/region/<string:name>')
api.add_resource(Powerplant, '/powerplant/<string:name>')
api.add_resource(PowerplantList, '/powerplants')

api.add_resource(RegionList, '/regions')
#api.add_resource(UserRegister, '/register')



api.add_resource(UserRegister, '/registration') # register endpoint


if __name__ == '__main__':
    from db import db
    db.init_app(app)
    app.run(port=5000, debug=True)
