var = "abcdefg"
print var

reversed = ""
for letter in var:
    temp = letter
    temp += reversed
    reversed = temp
print reversed