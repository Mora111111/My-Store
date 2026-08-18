import json
import re
import sys
import os

class mystoreBot:
    def __init__(self, db_path='chatbot_data.json'):
        script_dir = os.path.dirname(os.path.abspath(__file__))
        self.db_path = os.path.join(script_dir, db_path)
        self.intents = []
        self.load_intents()

    def load_intents(self):
        try:
            with open(self.db_path, 'r', encoding='utf-8') as file:
                data = json.load(file)
                self.intents = data.get("intents", [])
        except Exception as e:
            pass

    def clean_arabic_text(self, text):
        text = re.sub(r'[^\w\s]', ' ', text)
        text = text.replace('أ', 'ا').replace('إ', 'ا').replace('آ', 'ا')
        text = text.replace('ة', 'ه').replace('ى', 'ي')
        return text.split()

    def get_best_reply(self, user_message):
        if not self.intents:
            return "عذراً، نظام الرد الآلي قيد التحديث. سيقوم فريقنا بالرد قريباً."

        user_words = self.clean_arabic_text(user_message)
        best_reply = None
        highest_score = 0

        for intent in self.intents:
            score = 0
            for keyword in intent["keywords"]:
                clean_keyword_list = self.clean_arabic_text(keyword)
                if not clean_keyword_list:
                    continue
                clean_keyword = clean_keyword_list[0]
                if clean_keyword in user_words:
                    score += 1
            
            if score > highest_score:
                highest_score = score
                best_reply = intent["response"]

        if highest_score > 0:
            return best_reply
        else:
            return "تم استلام رسالتك. سيقوم ممثل خدمة العملاء بالرد عليك قريباً."

if __name__ == "__main__":
    if len(sys.argv) == 3:
        input_file = sys.argv[1]
        output_file = sys.argv[2]
        
        try:
            with open(input_file, 'r', encoding='utf-8') as f:
                incoming_msg = f.read().strip()
                
            bot = mystoreBot()
            reply = bot.get_best_reply(incoming_msg)
            
            with open(output_file, 'w', encoding='utf-8') as f:
                f.write(reply)
                
        except Exception as e:
            with open(output_file, 'w', encoding='utf-8') as f:
                f.write(f"Python Error: {str(e)}")