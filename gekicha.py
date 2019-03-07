import sys

def gekicha():

    return print("激しくチャリをこぐ")


if sys.argv[1] == "激チャ" :
    #sys.argvを使うときは[1]で指定する
    gekicha()



else :
    quit()
