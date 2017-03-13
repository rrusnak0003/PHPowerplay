from flask import Blueprint
from flask import render_template

store_blueprint = Blueprint('stores', __name__)

@store_blueprint.route('/')
def index():
    return render_template('stores/store_index.jinja2')

@store_blueprint.route('/store/<string:name>')
def store_page():
    pass
