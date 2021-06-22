#!C:\Python27 python3
#!/usr/bin/env python
       
from scipy import stats
import numpy as np
import sys
x = sys.argv[1]

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
        


df = np.array([final])
zscore_df = stats.zscore(df, axis=1)
data = list(zscore_df[0])


for i in range(len(data)):
    data[i]= float(data[i])
    



print(data)