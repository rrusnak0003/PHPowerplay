from flask_restful import Resource, reqparse
from flask_jwt import jwt_required
from models.powerplant import PowerplantModel


class Powerplant(Resource):
    parser = reqparse.RequestParser()
    parser.add_argument('revenue',
                        type=float,
                        required=True,
                        help="This field cannot be left blank!"
                        )

    parser.add_argument('region_id',
                        type=int,
                        required=True,
                        help="Every item needs a store id."
                        )

    @jwt_required()
    def get(self, name):
        powerplant = PowerplantModel.find_by_name(name)
        if powerplant:
            return powerplant.json()

        return{'message': 'Item not found'}, 404



    def post(self, name):
        if PowerplantModel.find_by_name(name):
            return {'message': "A powerplant with name '{}' already exists.".format(name)}, 400


        data = Powerplant.parser.parse_args()

        powerplant = PowerplantModel(name, **data)

        try:
            powerplant.save_to_db()
        except:
            return {"message": "An error occurred inserting the item."}, 500 #internal server error

        return powerplant.json(), 201






    def delete(self, name):
        powerplant = Powerplant.find_by_name(name)
        if powerplant:
            powerplant.delete_from_db()


        return {'message': 'Plant destroyed'}

    def put(self, name):
        data = Powerplant.parser.parse_args()

        powerplant = PowerplantModel.find_by_name(name)


        if powerplant is None:
            powerplant = Powerplant(name, **data)
        else:
            powerplant.price = data['price']
            powerplant.store_id = data['store_id']


        powerplant.save_to_db()
        return powerplant.json()






class PowerplantList(Resource):
    def get(self):
        return {'powerplant': [item.json() for item in PowerplantModel.query.all()]}