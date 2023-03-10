# geolocation_engine
takes in lat longs and searches which admin/political geoboundaries (in our postgres db) in our country they belong to.

Dictionary
GP - Gram Panchayat
TP - Taluk Panchayat
ZP - Zilla Panchayat
H - Hobli
W - Ward
AC - Assembly Constituency
LS - LokSabha Constituency

Boundaries covered:
| State  |GP   |TP   |ZP   |  H | W | AC | LS |
|---|---|---|---|---|
| Karnataka  | y  | n  | n  | y  | y | y | y |
| Assam  | y  | n  | n  | y  | n | n | n | n |
| Delhi NCT | n  | n  |n   | n  | y | y | y |
