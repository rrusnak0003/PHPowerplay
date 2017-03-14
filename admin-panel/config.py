import os
basedir = os.path.abspath(os.path.dirname(__file__))

CSRF_ENABLED = True
SECRET_KEY = '5Mwck8jS687LqZjc7BTuxHwcX4H8521p'

#DB parametes
SQLALCHEMY_DATABASE_URI = 'sqlite:///' + os.path.join(basedir, 'app.db')
SQLALCHEMY_TRACK_MODIFICATIONS = True

# Uncomment this to use MySQL database. User, password and database name are supposed to be stored in system environment variables
#db_user = str(os.getenv('db_user'))
#db_pass = str(os.getenv('db_password'))
#db_name = str(os.getenv('db_name'))
#SQLALCHEMY_DATABASE_URI = 'mysql://' + db_user + ':' + db_pass + '@localhost/' + db_name
#SQLALCHEMY_POOL_SIZE = 10