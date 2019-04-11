#テキストファイル(numbers.txt)から数字を一行ずつ読み込み、ユーザーが入力した倍数で掛け、テキストファイル(num_result.txt)に保存する


def baibai(txt_num, multi_num):

        numnum = txt_num * multi_num

        return numnum

#baibaiのファンクション内でInt形式にした方がいい、変数定義と関数定義が逆で可読性が低い
numnum = int()
txt_num = []
result_num = []


with open("./numbers.txt") as f:
    for line in f:
        txt_num.append(int(line.strip("\n")))


print("how many times do you want to multiply")

user_input = input(">> ")

multi_num = int(user_input)


for i in txt_num:
#iはtxt_numとして回せる
    result = baibai(i, multi_num)

    result_num.append(result)


with open('./num_result.txt', mode='wt') as f:
    for i in result_num:
        #配列(result_num)の何番目をiにできる
        f.write(str(i) + "\n")
