import sys

print "input = " + sys.argv[1], sys.argv[2]

tree = {'root': {'100': {'50': {'18': ['9', '27'], '36': ['31', '41']}, '75': {'62': ['56', '68'], '87': ['81', '93']}}, '150': {'125': {'112': ['106', '118'], '137': ['131', '143']}, '175': {'157': ['151', '163'], '187': ['181', '193']}}}};

print tree['root']['100']['75']