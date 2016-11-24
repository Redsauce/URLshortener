from django.conf.urls import url

from . import views

urlpatterns = [
    url(r'^$', views.home, name='home'),
    url(r'^(?P<short_id>[0-9a-zA-Z]+)$', views.redirect, name='redirect'),
    url(r'^shortify/$', views.shortify, name='shortify'),
]
