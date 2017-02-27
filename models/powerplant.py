from db import db

class PowerplantModel(db.Model):
    __tablename__ = 'powerplants'

    id = db.Column(db.Integer, primary_key=True)
    name = db.Column(db.String(80))
    revenue = db.Column(db.Float(precision=2))

    region_id = db.Column(db.Integer, db.ForeignKey('region.id'))
    region = db.relationship('RegionModel')

    def __init__(self, name, revenue, region_id):
        self.name = name
        self.revenue = revenue
        self.region_id= region_id

    def json(self):
        return {'name': self.name, 'revenue': self.price}


    @classmethod
    def find_by_name(cls, name):
        return cls.query.filter_by(name=name).first() #SELECT * FROM items WHERE name = name LIMIT 1


    def save_to_db(self):
        db.session.add(self)
        db.session.commit()


    def delete_from_db(self):
        db.session.delete(self)
        db.session.commit()
