import tkinter as tk
from tkinter import messagebox
import time
import json
import os

USER_DATA = "users.json"

CHAT = "chat.txt"

if not os.path.exists(USER_DATA):
    with open(USER_DATA, "w") as f:
        json.dump({}, f)

with open(USER_DATA, "r") as f:
    users = json.load(f)


def save_users():
    with open(USER_DATA, "w") as f:
        json.dump(users, f)

def register():
    username = username_entry.get()
    password = password_entry.get()

    if username in users:
        messagebox.showerror("Registration Failed", "Username already exists!")
    elif not username or not password:
        messagebox.showerror("Registration Failed", "Username or password cannot be empty.")
    else:
        users[username] = password
        save_users()
        messagebox.showinfo("Registration Success", "Account created! Please log in.")

def login():
    username = username_entry.get()
    password = password_entry.get()

    if username in users and users[username] == password:
        messagebox.showinfo("Login Success", f"Welcome, {username}!")
        login_frame.pack_forget()
        chat_frame.pack(fill="both", expand=True)
        load_chat_history()
    else:
        messagebox.showerror("Login Failed", "Invalid username or password.")

def send_message():
    message = message_entry.get()
    if message.strip():
        timestamp = time.strftime("%Y-%m-%d %H:%M:%S", time.localtime())
        formatted_message = f"You ({timestamp}): {message}\n"
        chat_area.config(state="normal")
        chat_area.insert("end", formatted_message)
        chat_area.config(state="disabled")
        save_message(formatted_message)
        message_entry.delete(0, "end")


def save_message(message):
    with open(CHAT, "a") as f:
        f.write(message)

def load_chat_history():
    if os.path.exists(CHAT):
        with open(CHAT, "r") as f:
            chat_area.config(state="normal")
            chat_area.insert("end", f.read())
            chat_area.config(state="disabled")

def clear_chat():
    chat_area.config(state="normal")
    chat_area.delete("1.0", "end")
    chat_area.config(state="disabled")
    if os.path.exists(CHAT_HISTORY_FILE):
        open(CHAT, "w").close()

def logout():
    chat_frame.pack_forget()
    login_frame.pack(fill="both", expand=True)
    username_entry.delete(0, "end")
    password_entry.delete(0, "end")

root = tk.Tk()
root.title("Chat Application")
root.geometry("500x600")

login_frame = tk.Frame(root)
login_frame.pack(fill="both", expand=True)

tk.Label(login_frame, text="Login or Register", font=("Arial", 18)).pack(pady=20)
tk.Label(login_frame, text="Username:").pack(pady=5)
username_entry = tk.Entry(login_frame)
username_entry.pack(pady=5)

tk.Label(login_frame, text="Password:").pack(pady=5)
password_entry = tk.Entry(login_frame, show="*")
password_entry.pack(pady=5)

login_button = tk.Button(login_frame, text="Login", command=login)
login_button.pack(pady=10)

register_button = tk.Button(login_frame, text="Register", command=register)
register_button.pack(pady=10)

chat_frame = tk.Frame(root)

chat_area = tk.Text(chat_frame, state="disabled", wrap="word", bg="#f4f4f4", fg="#333")
chat_area.pack(fill="both", expand=True, padx=10, pady=10)

message_frame = tk.Frame(chat_frame)
message_frame.pack(fill="x", padx=10, pady=10)

message_entry = tk.Entry(message_frame)
message_entry.pack(side="left", fill="x", expand=True, padx=5)

send_button = tk.Button(message_frame, text="Send", command=send_message)
send_button.pack(side="left", padx=5)

clear_button = tk.Button(chat_frame, text="Clear Chat", command=clear_chat)
clear_button.pack(pady=5)

logout_button = tk.Button(chat_frame, text="Logout", command=logout)
logout_button.pack(pady=5)

root.mainloop()