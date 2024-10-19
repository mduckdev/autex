import pytest
import requests
import random
import string

# URL do rejestracji
REGISTER_URL = 'http://localhost/autex/www/register.php'
def gen_random_str(length):
    return ''.join(random.choices(string.ascii_letters, k=length))
# Dane do rejestracji
VALID_USERNAME_UNIQUE = 'testuser'+gen_random_str(10)+"@example.com"
VALID_USERNAME = 'testuser@example.com'
VALID_PASSWORD = 'testpassword'



def register(email, password,password_repeat, first_name=None, last_name=None):
    # Pobierz token CSRF
    session = requests.Session()
    response = session.get(REGISTER_URL)
    csrf_token = response.text.split('name="csrf_token" value="')[1].split('"')[0]
    cookie = {'PHPSESSID': requests.utils.dict_from_cookiejar(response.cookies)['PHPSESSID']}

    # Dane do przesłania w formularzu
    data = {
        'csrf_token': csrf_token,
        'email': email,
        'password': password,
        'passwordRepeat': password_repeat,
        'firstName': first_name if first_name else '',
        'lastName': last_name if last_name else ''
    }

    # Wysyłanie POST requestu
    response = session.post(REGISTER_URL, data=data, cookies=cookie)
    return response

def test_register_success():
    response = register(VALID_USERNAME_UNIQUE, VALID_PASSWORD, VALID_PASSWORD)
    # Sprawdź, czy rejestracja zakończyła się sukcesem
    assert response.url == 'http://localhost/autex/www/login.php'

@pytest.mark.parametrize("email, password, password_repeat, first_name, last_name, expected_error", [
    ('', VALID_PASSWORD,VALID_PASSWORD, None, None, 'Nieprawidłowy adres email.'),
    ('toolongemail1234567890', VALID_PASSWORD,VALID_PASSWORD, None, None, 'Nieprawidłowy adres email.'),
    (VALID_USERNAME, '','', None, None, 'Hasło musi zawierać od 10 do 128 znaków.'),
    (VALID_USERNAME, 'short','short', None, None, 'Hasło musi zawierać od 10 do 128 znaków.'),
    (VALID_USERNAME, 'toolongpasswordtoolongpasswordtoolongpasswordtoolongpasswordtoolongpasswordtoolongpasswordtoolongpasswordtoolongpasswordtoolongpassword','toolongpasswordtoolongpasswordtoolongpasswordtoolongpasswordtoolongpasswordtoolongpasswordtoolongpasswordtoolongpasswordtoolongpassword', None, None, 'Hasło musi zawierać od 10 do 128 znaków.'),
    (VALID_USERNAME, VALID_PASSWORD,'incorrectpassword', None, None, 'Hasła się nie zgadzają.'),
])
def test_register_invalid_input(email, password,password_repeat, first_name, last_name, expected_error):
    response = register(email, password,password_repeat, first_name, last_name)
    # Sprawdź, czy wyświetlany jest oczekiwany komunikat błędu
    assert expected_error in response.text
