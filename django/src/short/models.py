from mongoengine import *
import datetime

class ShortURL(Document):
    target = StringField(max_length=500)
    short = StringField(max_length=20)
    access_count = IntField(default=0)
    access_list = ListField()

    def get_target(self, request):
        self.register_access(request)
        self.save()
        return self.target

    def register_access(self, request):
        self.access_count = self.access_count + 1
        self.access_list.append([datetime.datetime.now(),self.get_client_ip(request)])

    def get_client_ip(self, request):
        x_forwarded_for = request.META.get('HTTP_X_FORWARDED_FOR')
        if x_forwarded_for:
            ip = x_forwarded_for.split(',')[0]
        else:
            ip = request.META.get('REMOTE_ADDR')
        return ip
