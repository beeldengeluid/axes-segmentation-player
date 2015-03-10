from flask import Flask
from flask import render_template
from flask import request


app = Flask(__name__)
app.debug = True

@app.route('/')
def home():
	return render_template('index.tpl.php')

if __name__ == '__main__':
	app.run()