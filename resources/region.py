from flask_restful import Resource
from models.region import RegionModel

class Region(Resource):
    def get(self, name):
        region = RegionModel.find_by_name(name)
        if region:
            return region.json()
        return{'message': 'Store not found'}, 404


    def post(self, name):
        if RegionModel.find_by_name(name):
            return{'message': "A region with name '{}' already exists.".format(name)}, 400

        region = RegionModel(name)
        try:
            region.save_to_db()
        except:
            return {'message': 'An error occurred while creating the region.'}, 500
        return region.json(), 201

    def delete(self, name):
        region = RegionModel.find_by_name(name)
        if region:
            region.delete_from_db()
        return{'message': 'Region deleted'}


class RegionList(Resource):
    def get(self):
        return{'regions': [region.json() for region in RegionModel.query.all()]}
