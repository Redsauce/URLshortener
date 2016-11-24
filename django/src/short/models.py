from mongoengine import *

class ShortURL(Document):
    target = StringField(max_length=500)
    short = StringField(max_length=20)
