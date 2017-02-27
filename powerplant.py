import sqlite3
from flask_restful import Resource, reqparse
from flask_jwt import jwt_required



class Powerplant(Resource):
    parser = reqparse.RequestParser()
    parser.add_argument('price',
                        type=float,
                        required=True,
                        help="This field cannot be left blank!"
                        )

    @jwt_required()
    def get(self, name):
        connection = sqlite3.connect('data.db')
        cursor = connection.cursor()

        query = "SELECT * FROM powerplants WHERE name=?"
        result = cursor.execute(query, (name,))
        row = result.fetchone()
        connection.close()

        if row:
            return {'powerplant': {'name': row[0], 'price': row[1]}}
        return{'message': 'Powerplant not found'}, 404

    def post(self, name):
        if next(filter(lambda x: x['name'] == name, powerplants), None) is not None:
            return {'message': "An powerplant with name '{}' already exists.".format(name)}, 400

        #data = request.get_json()
        data = Powerplant.parser.parse_args()

        powerplant = {'name': name, 'price': data['price']}
        powerplants.append(powerplant)
        return powerplant, 201


    def delete(self, name):
        global powerplants
        powerplants = list(filter(lambda x: x['name'] !=name, powerplants))
        return {'message': 'Powerplant deleted'}

    def put(self, name):



        data = Powerplant.parser.parse_args()

        #print(data['another'])

        powerplant = next(filter(lambda x: x['name'] == name, powerplants), None)

        if powerplant is None:
            powerplant = {'name': name, 'price': data['price']}
            powerplants.append(powerplant)
        else:
            powerplant.update(data)
        return powerplant

class PowerplantList(Resource):
    def get(self):
        return {'powerplants': powerplants}

