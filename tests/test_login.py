import pytest
import requests

# URL do logowania
LOGIN_URL = 'http://localhost/autex/www/login.php'

# Dane do logowania
VALID_USERNAME = 'admin'
VALID_PASSWORD = 'adminadmin'
INVALID_USERNAME = 'wronguser'
INVALID_PASSWORD = 'wrongpassword'
 # Pobierz token CSRF
session = requests.Session()
response = session.get(LOGIN_URL)
csrf_token = response.text.split('name="csrf_token" value="')[1].split('"')[0]
cookie = {'PHPSESSID': requests.utils.dict_from_cookiejar(response.cookies)['PHPSESSID']}
def login(email, password):
   
    # Dane do przesłania w formularzu
    data = {
        'csrf_token': csrf_token,
        'email': email,
        'password': password
    }
    # Wysyłanie POST requestu
    response = session.post(LOGIN_URL, data=data,cookies=cookie)
    print(response.text)
    return response

def test_login_success():
    response = login(VALID_USERNAME, VALID_PASSWORD)
    assert response.url == 'http://localhost/autex/www/index.php'

def test_login_wrong_password():
    response = login(VALID_USERNAME, INVALID_PASSWORD)
    assert "Adres e-mail lub hasło jest niepoprawne" in response.text

def test_login_wrong_email():
    response = login(INVALID_USERNAME, VALID_PASSWORD)
    assert "Adres e-mail lub hasło jest niepoprawne" in response.text

def test_login_wrong_email_and_password():
    response = login(INVALID_USERNAME, INVALID_PASSWORD)
    assert "Adres e-mail lub hasło jest niepoprawne" in response.text
