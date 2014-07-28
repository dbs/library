#!/usr/bin/python
"""
A script which searches a DSpace database for any items under
collections named "Masters Theses", "Doctoral Theses", and "Undergraduate
Theses", and adds them to a top level Electronic Theses community

Best run as a cronjob
"""

import psycopg2

# the id's of the top level masters/doctoral/undergrad theses collections
UNDERGRAD_ID = '103'
MASTERS_ID = '104'
DOCTORAL_ID = '105'

# database settings
# XXX switch to using .pgpass
DB = {}
DB['USER'] = "my_db_user"
DB['PASSWORD'] = "my_db_password"
DB['NAME'] = "my_db_name"


masters = {'collection': 'Masters Theses', 'id': MASTERS_ID}
masters2 = {'collection': "Master's Theses%", 'id': MASTERS_ID}
undergrad = {'collection': 'Undergraduate Theses', 'id': UNDERGRAD_ID}
doctoral = {'collection': 'Doctoral Theses', 'id': DOCTORAL_ID}

def get_items(type):
  conn = psycopg2.connect("dbname=%s user=%s" % (DB['NAME'], DB['USER']) )
  cur = conn.cursor()
  #find all items that aren't in the top level collection yet
  items_query = """select item_id from item where owning_collection in
                     (select collection_id from collection
                        where name = %s and collection_id != %s) and item_id
                        not in (select item_id from collection2item where
                        collection_id = %s);"""
  cur.execute(items_query, (type['collection'], type['id'], type['id'],))
  items = cur.fetchall()
  return items

def sync_items(items, type):
  "our items should be in the form of a list of tuples"
  if not items:
    return None

  conn = psycopg2.connect("dbname=%s user=%s" % (DB['NAME'], DB['USER']))
  cur = conn.cursor()

  for i in items:
    item_insert = """insert into collection2item (id, collection_id, item_id)
                       values (nextval('collection2item_seq'), %s, %s);"""
    cur.execute(item_insert, (type['id'], i[0],))
  conn.commit()

def add_publisher():
  """
  Add a publisher to any items that don't have one

  This is a requirement for LAC-BAC as of late 2013.
  """

  try:
    conn = psycopg2.connect("dbname=dspace user=dspace")
    cur = conn.cursor()

    cur.execute("""
      INSERT INTO public.metadatavalue (item_id, metadata_field_id, text_value,
      text_lang) SELECT item_id, '39', 'Laurentian University of Sudbury', 'en_CA'
      FROM nonpublished;
    """)
    conn.commit()
  except:
    print("Failed to add publisher metadata value")

if __name__ == '__main__':
  # Run for undergrad, masters, and doctoral

  undergrad_items = get_items(undergrad)
  masters_items = get_items(masters)
  masters2_items = get_items(masters2)
  doctorate_items = get_items(doctoral)

  sync_items(undergrad_items, undergrad)
  sync_items(masters_items, masters)
  sync_items(masters2_items, masters2)
  sync_items(doctorate_items, doctoral)

  add_publisher()
