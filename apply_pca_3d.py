#!C:\Python27 python3
#!/usr/bin/env python
       
from scipy import stats
import numpy as np
import sys
from sklearn.decomposition import PCA
from sklearn.preprocessing import StandardScaler

def get_vect(x):
    x = x[1:]
    nex = False
    i = 0
    valor = ""
    final = []
    while(i<len(x)):
        if(x[i] == "," or x[i] == "]"):
            final.append(float(valor))
            valor = ""
            nex = True
            i=i+1
        else:
            valor = valor + x[i]
            i=i+1
            nex = False
        



    return final

num = int(sys.argv[1])

x = []
for i in range(num-1):
    X = sys.argv[i+2]
    x.append(X)





fin = []
#print(x)

for variable in range(len(x)):
    var = get_vect(x[variable])
    fin.append(var)


pca = PCA(n_components=3)
principalComponents = pca.fit_transform(fin)

final_data = []
for i in principalComponents:
    final_data.append([i[0],i[1],i[2]])

print(final_data)
