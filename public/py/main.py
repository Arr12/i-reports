import httplib2
import os
from apiclient import discovery
from google.oauth2 import service_account
from oauth2client.service_account import ServiceAccountCredentials
scopes = [
          "https://www.googleapis.com/auth/drive",
          "https://www.googleapis.com/auth/drive.file",
          "https://www.googleapis.com/auth/spreadsheets"
          ]

secret_file = os.path.join(os.getcwd(), 'credentials.json')

credentials = service_account.Credentials.from_service_account_file(secret_file, scopes=scopes)
sheets_service = discovery.build('sheets', 'v4', credentials=credentials)
drive_service = discovery.build('drive', 'v3', credentials=credentials)


from typing import Optional
from fastapi import FastAPI

app = FastAPI()
EMAIL = 'wavelit1@gmail.com'

@app.get("/create-folder/{nama_folder}")
def read_root(nama_folder):
    file_metadata = {
        'name': nama_folder,
        'mimeType': 'application/vnd.google-apps.folder'
    }

    file = drive_service.files().create(body=file_metadata,
                                        fields='id').execute()

    folder_id = file.get('id')
    # PERMISSIONS UPDATE
    new_permissions = {
        'type': 'anyone',
        'role': 'writer',
        # 'emailAddress': EMAIL
        # 'type': 'domain',
        # 'role': 'writer',
        # 'domain': 'gmail.com'
    }
    permission_response = drive_service.permissions().create(
    fileId = folder_id, body=new_permissions).execute()
    return [folder_id,permission_response]

@app.get("/create-spreadsheet/{folder_id}/{nama_file}")
def create_spreadsheet(folder_id, nama_file):
    # SPREADSHEET CREATION
    spreadsheet = {
        'properties': {
            'title': nama_file
        }
    }
    creation_response = sheets_service.spreadsheets().create(body=spreadsheet,
    fields='spreadsheetId').execute()
    spreadsheet_id = creation_response.get('spreadsheetId')
    # permission
    new_file_permission = {
        'type': 'anyone',
        'role': 'writer',
        # 'emailAddress': EMAIL
        # 'type': 'domain',
        # 'role': 'writer',
        # 'domain': 'gmail.com'
    }
    permission_response = drive_service.permissions().create(
    fileId=spreadsheet_id, body=new_file_permission).execute()
    ## MOVE SPREADSHEET TO FOLDER
    get_parents_response = drive_service.files().get(fileId=spreadsheet_id,
    fields='parents').execute()

    previous_parents = ",".join(get_parents_response.get('parents'))

    move_response = drive_service.files().update(fileId=spreadsheet_id,
    addParents=folder_id,
    removeParents=previous_parents,
    fields='id, parents').execute()
    return {
        'spreadsheet_id' : spreadsheet_id,
        'creation_response' : creation_response,
        'permission_response' : permission_response,
        'move_response' : move_response,
    }

@app.get("/duplicate-spreadsheet/{old_file}/{folder_sid}/{name_file}")
def create_spreadsheet(old_file, folder_sid, name_file):
    import gspread
    from gspread.models import Cell, Spreadsheet

    scope = [
        "https://www.googleapis.com/auth/spreadsheets.readonly",
        "https://www.googleapis.com/auth/spreadsheets",
        "https://www.googleapis.com/auth/drive.readonly",
        "https://www.googleapis.com/auth/drive.file",
        "https://www.googleapis.com/auth/drive",
    ]

    json_key_absolute_path = "credentials.json"
    credentials = ServiceAccountCredentials.from_json_keyfile_name(json_key_absolute_path, scope)
    client = gspread.authorize(credentials)
    client.copy(old_file, title=name_file, copy_permissions=True)
    sht = client.open(name_file)
    worksheet = sht.get_worksheet(0)
    spreadsheetId = worksheet.spreadsheet.id
    
    ## MOVE SPREADSHEET TO FOLDER
    get_parents_response = drive_service.files().get(fileId=spreadsheetId,
    fields='parents').execute()

    previous_parents = ",".join(get_parents_response.get('parents'))

    move_response = drive_service.files().update(fileId=spreadsheetId,
    addParents=folder_sid,
    removeParents=previous_parents,
    fields='id, parents').execute()
    return {
        'move_response' : move_response
    }

# @app.get("/items/{item_id}")
# def read_item(item_id: int, q: Optional[str] = None):
#     return {"item_id": item_id, "q": q}
