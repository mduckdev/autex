import pytest
import requests

# URL do rejestracji
REGISTER_URL = 'http://localhost/autex/www/register.php'

# Dane do rejestracji
VALID_USERNAME = 'testuser'
VALID_PASSWORD = 'testpassword'

def register(username, password, first_name=None, last_name=None):
    # Pobierz token CSRF
    session = requests.Session()
    response = session.get(REGISTER_URL)
    csrf_token = response.text.split('name="csrf_token" value="')[1].split('"')[0]
    cookie = {'PHPSESSID': requests.utils.dict_from_cookiejar(response.cookies)['PHPSESSID']}

    # Dane do przesłania w formularzu
    data = {
        'csrf_token': csrf_token,
        'username': username,
        'password': password,
        'passwordRepeat': password,
        'firstName': first_name if first_name else '',
        'lastName': last_name if last_name else ''
    }

    # Wysyłanie POST requestu
    response = session.post(REGISTER_URL, data=data, cookies=cookie)
    return response

def test_register_success():
    response = register(VALID_USERNAME, VALID_PASSWORD)
    # Sprawdź, czy rejestracja zakończyła się sukcesem
    assert response.url == 'http://localhost/autex/www/login.php'

@pytest.mark.parametrize("username, password, first_name, last_name, expected_error", [
    ('', VALID_PASSWORD, None, None, 'Nazwa użytkownika musi zawierać od 1 do 20 znaków.'),
    ('toolongusername1234567890', VALID_PASSWORD, None, None, 'Nazwa użytkownika musi zawierać od 1 do 20 znaków.'),
    (VALID_USERNAME, '', None, None, 'Hasło musi zawierać od 10 do 128 znaków.'),
    (VALID_USERNAME, 'short', None, None, 'Hasło musi zawierać od 10 do 128 znaków.'),
    (VALID_USERNAME, VALID_PASSWORD, 'TooLongFirstName', None, 'Imię musi zawierać od 1 do 50 znaków.'),
    (VALID_USERNAME, VALID_PASSWORD, None, 'TooLongLastName', 'Nazwisko musi zawierać od 1 do 50 znaków.'),
    (VALID_USERNAME, 'incorrectpassword', None, None, 'Hasła się nie zgadzają.'),
])
def test_register_invalid_input(username, password, first_name, last_name, expected_error):
    response = register(username, password, first_name, last_name)
    # Sprawdź, czy wyświetlany jest oczekiwany komunikat błędu
    assert expected_error in response.text
