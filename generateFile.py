import csv
from faker import Faker

l=Faker('en_GB') 
f=open("test.csv","a")
k=csv.reader(f)
w=csv.writer(f)
# rows = pow(10,6)*13
rows = pow(10,5)
w.writerow(('firstName','lastName','email'))
    
for i in range(rows):
    print(i*100/rows, "%")
    n = l.first_name()
    ln = l.last_name()
    e = l.email()

    w.writerow((
        n,
        ln,
        e
    ))

f.close()
print("Done")