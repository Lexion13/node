import requests,json,base64
from pydub import AudioSegment
import datetime


#コードの視認性を上げるために、変数がリストということを明示、かつ、パラメータを各々変数にすることにより今後の変更、追加を容易にする。

dt_now = datetime.datetime.now()
dt_now = str(dt_now.strftime('%Y%m%d%H%M%S'))

raw_love = {}
raw_lie = {}

tts_text_love = 'あいしてる'
tts_text_lie = 'うそだよ'
tts_speaker = 'hikari'
tts_emotion = 'happiness'
tts_emotion_level = '2'
tts_pitch= '100'
tts_speed = '100'
tts_volume = '100'

raw_love = {
    'tts[text]': tts_text_love,
    'tts[speaker]': tts_speaker,
    'tts[emotion]': tts_emotion,
    'tts[emotion_level]': tts_emotion_level,
    'tts[pitch]': tts_pitch,
    'tts[speed]': tts_speed,
    'tts[volume]': tts_volume
}

raw_lie = {
    'tts[text]': tts_text_lie,
    'tts[speaker]': tts_speaker,
    'tts[emotion]': tts_emotion,
    'tts[emotion_level]': tts_emotion_level,
    'tts[pitch]': tts_pitch,
    'tts[speed]': tts_speed,
    'tts[volume]': tts_volume
}

#apiは仕様変更、使い回しが多数発生するケースが多い、よって変数に格納

voice_api = 'http://voice-text-api-explorer.herokuapp.com/voice_text_api.php'

def save_voice(raw,name) :

    response = requests.post(voice_api, data=raw)

    encoded = response.json()["data"].strip('data:audio/wav;base64,')

    decoded = base64.b64decode(encoded)

#ファイルの重複保存を避けるため、ファイル名に時間を追記
    with open('voicy_' + name + dt_now + '.wav', 'wb') as f :

        f.write(decoded)

    return 'voicy_' + name + dt_now + '.wav'

def link_voice(name_list,loop_count) :

    voice_file = AudioSegment.empty()

    voice_love = AudioSegment.from_file('./' + name_list[0], "wav")

    voice_lie = AudioSegment.from_file('./' + name_list[1], "wav")

    for i in range(0,loop_count) :

        if i % 4 == 0 :

            voice_file += voice_lie

        else :

            voice_file += voice_love

    i += 1

    print(i)

    voice_file.export("voice_file.wav", format="wav")

#そもそも100でよくない？w
link_voice([save_voice(raw_love,"love"),save_voice(raw_lie,"lie")],100)
