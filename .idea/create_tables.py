import sqlite3
connection = sqlite3.connect('data.db')
cursor = connection.cursor()


#create temp sqlite3 database to hold users. Will be replaced with main projects sql instance.
create_table = "CREATE TABLE IF NOT EXISTS users (id INTEGER PRIMARY KEY , username text, password text)"
cursor.execute(create_table)

connection.commit()
connection.close()