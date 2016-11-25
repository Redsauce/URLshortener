from django.shortcuts import render
from django.http import HttpResponseRedirect, Http404
from django.urls import reverse
from .models import ShortURL
import os

def home(request):
    return render(request, 'short/index.html', {})

def redirect(request, short_id):
    short = ShortURL.objects(short=short_id).first()
    if short:
        return HttpResponseRedirect(short.target)
    else:
        raise Http404("The short link does not exist")


def success(request, short_id):
    return render(request, 'short/success.html', {'short_id': short_id})

def shortify(request):
    url = request.POST.get('url')
    if url:
        short_id = get_unique_short_id(10)
        ShortURL.objects.create(target=url,short=short_id)
        return HttpResponseRedirect(reverse('short:success', args=(short_id,)))
    else:
        return HttpResponseRedirect(reverse('short:home'))

def get_unique_short_id(size):
    valid_bytes = "1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"
    found = False
    while found == False:
        res = ""
        for i in range(size):
            res += valid_bytes[ord(os.urandom(1)) % len(valid_bytes)]
        if len(ShortURL.objects(short=res)) == 0:
            found = True
    return res
