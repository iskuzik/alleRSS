# alleRSS
alleRSS to skrypt tworzenia kanałów RSS z nowymi ofertami z serwisu Allegro.pl. Możemy w nim ustalić parametry, które muszą spełniać oferty, żeby wyświetlane były w kanale RSS. Powstał on na skutek usunięcia z serwisu Allegro.pl generatora kanałów RSS (dostępny pod linkami: allegro.pl/rss.php/generatorSearch, allegro.pl/rss.php/generatorCat oraz allegro.pl/rss.php/generatorUser). Działa przy pomocy [WebAPI](https://allegro.pl/webapi). Niestety nie jest on idealny (więcej o tym w [uwagach](#uwagi)).

# Instalacja
1. Pobieramy skrypt 
1. Dodajemy do skryptu nasz klucz do WebAPI (możemy go wygenerować [tutaj](http://allegro.pl/myaccount/webapi.php/generateNewKey))
1. Zmodyfikowany skrypt wysyłamy na własny serwer, bądź na nasz lokalny serwer z obsługą PHP
1. Tworzymy link do kanału RSS (więcej o tym niżej)
1. Sprawdzamy poprawność naszego linku w przeglądarce
1. Dodajemy link do naszego czytnika kanałów RSS

# Tworzenie linku do kanału RSS

Link do kanału RSS tworzymy poprzez podanie adresu serwera na jaki wysłaliśmy skrypt, a następnie podaniu ścieżki do skryptu. Przykładowo, gdy wysłaliśmy skrypt do głównego katalogu serwera, to podstawowym adresem kanału RSS będzie:
```
http://www.naszadomena.pl/alleRSS.php?
```
Do takiego adresu dodajemy kolejne parametry. Przed każdym kolejnym parametrem dodajemy znak `&`. Poniżej podałem kilka przykładowe linki do kanałów z wykorzystaniem parametrów.

## 1. Wymagane parametry
Użycie jednego z poniższych parametrów jest wymagane, żeby wyświetlić jakiekolwiek oferty. Oczywiście możemy wykorzystać też dwa lub trzy z tych parametrów jednocześnie.

### Wyszukiwana fraza: `string`
W parametrze `string` określamy frazę, która będzie wyszukiwana w tytułach ofert. Jeśli składa się ona z kilku wyrazów, spacje zamieniamy na znak `+`. Przykładowo:
```
string=szukany+przedmiot
```

### ID kategorii: `categoryId`
W parametrze `categoryId` możemy podać ID kategorii, z której chcemy wyświetlać oferty. Przykładowo:
```
categoryId=348
```
Niestety aktualnie ID kategorii musimy wyszukać ręcznie wchodząc do wybranej kategorii, np. [Akcesoria GSM](https://allegro.pl/kategoria/akcesoria-gsm-348). Tam w pasku adresu, po nazwie kategorii mozemy znaleźć numer, który jest właśnie ID kategorii.

### ID użytkownika: `userId`
W parametrze `userId` możemy podać ID użytkownika, którego oferty chcemy wyświetlać. Przykładowo:
```
userId=1680
```
Niestety aktualnie ID użytkownika musimy wyszukać ręcznie wchodząc do wybranej profilu, np. [Allegro](https://allegro.pl/uzytkownik/Allegro). Tam możemy kliknąć na "Oceny i komentarze", po czym bez problemu zidentyfikujemy ID tego użytkownika.

## 2. Opcjonalne parametry
### Wyszukiwanie w opisach i parametrach ofert: `description`
Parametr `description` określa, czy oprócz szukania naszej frazy w tytułach, chcemy jej też szukać w opisach i parametrach ofert. Jeśli chcemy rozszerzyć wyszukiwanie, to dodajemy parametr:
```
description=1
```
### Typ wyszukiwania: `searchType`
Parametr `searchType` pozwala nam określić typ wyszukiwania. Domyślnie wyświetlane są oferty, w których znajdują się wszystkie podane słowa, niekoniecznie w podanej przez nas kolejności. Jednak możemy to zmienić.
#### Szukanie dokładnie podanej frazy
W tym przypadku wyszukiwane będą tylko oferty, które zawierają podana przez nas frazę dokładnie w taki sposób, jaki wpisaliśmy w parametrze `string`. Sprawdzane będą też polskie znaki, więc warto zwrócić na nie uwagę przy wykorzystaniu tego parametru.
```
searchType=2
```
#### Szukanie któregokolwiek ze słów
Podając parametr `searchType` z wartością `3` wyświetlać będziemy wszystkie oferty, które zawierają przynajmniej jedno z podanych przez nas słów.
```
searchType=3
```
### Wyłączenie słów z wyszukiwania: `exclude`
W tym parametrze możemy podać słowa, które nie mają znajdować się w wyświetlonych ofertach. Dzięki temu pozbędziemy się podobnych ofert do tej, której szukamy. Może nam to oszczędzić wiele czasu na sprawdzanie ofert, którymi na pewno nie jesteśmy zainteresowani. Kolejne słowa możemy podawać ze znakiem `+`, przykładowo:
```
exclude=ram+cpu+gpu
```

### Cena od: `price_from`
W parametrze `price_from` możemy określić minimalną cenę, od której powinny zaczynać się oferty. Możemy w nim podawać liczby całkowite, np. `1000`, jak i zmiennoprzecinkowe, np. `999.99`. Przykładowo:
```
price_from=150.50
```
### Cena do: `price_to`
W parametrze `price_to` możemy określić maksymalną cenę w jakiej wyświetlane będą oferty. Możemy w nim podawać liczby całkowite, np. `1000`, jak i zmiennoprzecinkowe, np. `999.99`. Przykładowo:
```
price_to=300
```
### Typ ofert: `offerType`
W parametrze `offerType` możemy sprecyzować typy ofert, które będą dostarczane dla nas w kanale. Domyślnie wyświetlane są licytacje i oferty kup teraz. Dodając ten parametr, możemy wyświetlić jedynie aukcje (`auction`) lub oferty kup teraz (`buyNow`). Przykładowo:
```
offerType=buyNow
```
lub
```
offerType=auction
```
### Stan: `condition`
W parametrze `condition` możemy określić stan przedmiotów, jakie powinny być wyświetlane w naszym kanale. Dostępne są dwie opcje, jedynie nowe (`new`) oraz jedynie używane (`used`). Domyślnie (bez parametru) wyświetlane są wszystkie rodzaje przedmiotów, włącznie z tymi, w których nie określono ich stanu.
```
condition=new
```
lub
```
condition=used
```
### Parametry lokalizacyjne
Parametry określające lokalizację są specjalne, gdyż jednocześnie możemy wykorzystać tylko jeden z nich. Jednak skrypt jest stworzony tak, że nawet jeśli w adresie podamy więcej parametrów określających lokalizację, to wybrany zostanie tylko jeden, w kolejności: miasto, odległość i województwo.
#### Miasto: `city`
Parametr `city` pozwala określić miasto, z jakiego wyświetlane będą oferty. Tutaj także w przypadku nazw miast składających się z dwóch (lub więcej) wyrazów, specję zastępujemy znakiem `+`. Przykładowo:
```
city=Nowy+Sącz
```
#### Dystans: `distance` oraz `postCode`
Parametr `distance` pozwala określić odległość, z jakiej będą wyświetlane oferty. Jednak do swojego działania wymaga również parametru `postCode`, w którym podajemy kod pocztowy od którego będzie obliczana odległość. Przykładowo:
```
distance=25km&postCode=00-001
```
W parametrze `distance` możemy podać jedną z poniższych wartości:

Wartość parametru | Dystans
:---: | ---
10km | 10 km
25km | 25 km
50km | 50 km
75km | 75 km
100km | 100 km
150km | 150 km
200km | 200 km
350km | 350 km
500km | 500 km

#### Województwo: `state`
W parametrze `state` możemy określić z jakiego województwa zostaną wyświetlone oferty. Przykładowo:
```
state=11
```
W parametrze `state` możemy podać jedną z poniższych wartości:

Wartość parametru | Województwo
:---: | ---
1 | dolnośląskie
2 | kujawsko-pomorskie
3 | lubelskie
4 | lubuskie
5 | łódzkie
6 | małopolskie
7 | mazowieckie
8 | opolskie
9 | podkarpackie
10 | podlaskie
11 | pomorskie
12 | śląskie
13 | świętokrzyskie
14 | warmińsko-mazurskie
15 | wielkopolskie
16 | zachodniopomorskie

### Opcje oferty
#### Darmowa dostawa `freeShipping`
W tym parametrze możemy zaznaczyć, że interesują nas jedynie oferty, które oferują opcję darmowej dostawy. W takim przypadku musimy dodać parametr:
```
freeShipping=1
```
#### Darmowy zwrot `freeReturn`
W tym parametrze możemy zaznaczyć, że interesują nas jedynie oferty, które oferują opcję darmowego zwrotu. W takim przypadku musimy dodać parametr:
```
freeReturn=1
```
#### Odbiór w paczkomacie `generalDelivery`
W tym parametrze możemy zaznaczyć, że interesują nas jedynie oferty, które oferują opcję odbioru paczki w paczkomacie. W takim przypadku musimy dodać parametr:
```
generalDelivery=1
```
#### Raty PayU `installmentAvailable`
W tym parametrze możemy zaznaczyć, że interesują nas jedynie oferty, które oferują opcję rat w PayU. W takim przypadku musimy dodać parametr:
```
installmentAvailable=1
```
#### Faktura VAT `vatInvoice`
W tym parametrze możemy zaznaczyć, że interesują nas jedynie oferty, które przy zakupie umożliwiają wystawienie faktury VAT. W takim przypadku musimy dodać parametr:
```
vatInvoice=1
```
#### Odbiór osobisty: `personalReceipt`
W tym parametrze możemy zaznaczyć, że interesują nas jedynie oferty, które oferują opcję odbioru osobistego. W takim przypadku musimy dodać parametr:
```
personalReceipt=1
```

# Przykładowe wykorzystanie parametrów w kanałach RSS

- Najprostszy kanał RSS z wykorzystaniem jedynie parametru `string`
```
http://www.naszadomena.pl/alleRSS.php?string=szukany+przedmiot
```
- Kanał wykorzystujący większość dostepnych parametrów jednocześnie
```
http://www.naszadomena.pl/alleRSS.php?string=szukany+przedmiot&categoryId=348&userId=1680&description=1&price_from=9.99&price_to=1000&offerType=buyNow&condition=new&personalReceipt=1&distance=10km&postCode=00-001
```


# Uwagi
- Skrypt niestety nie jest idealny, funkcja `doGetItemsList` w WebAPI działa w taki sposób, że najpierw wyświetlane są oferty promowane, a następnie dopiero te niepromowane. Powoduje to taki problem, że jeśli ilość zwracanych ofert jest mniejsza niż łączna ilość promowanych ofert, to nie będziemy informowani o najnowszych ofertach niepromowanych. Dlatego, jeśli chcemy otrzymywać informacje o wszystkich nowych ofertach, to bezpiecznym rozwiązaniem będzie stworzenie takiego zapytania, w którym liczba promowanych ofert nie jest większa, niż połowa zwracanych wyników. Jeśli jest większa, to możemy zwiększyć liczbę zwracanych wyników (4 linia w kodzie), bądź - co zalecam - bardziej sprecyzować nasze wyszukiwanie, np. dodając dodatkowe parametry do niego. Liczbę zwracanych ofert promowanych w kanale RSS możemy podejrzeć w opisie kanału.

- Pamiętajmy o limicie przydzielonym dla każdego klucza WebAPI: 9000 zapytań na minutę oraz limicie dla IP: 120 zapytań na sekundę.


# TODO
- [x] informacje o nowych ofertach na podstawie wyników wyszukiwania
- [x] informacje o nowych ofertach w wybranej kategorii
- [x] informacje o nowych ofertach danego użytkownika
- [ ] graficzne tworzenie linków do kanałów RSS z ofertami



# LICENCJA

MIT

