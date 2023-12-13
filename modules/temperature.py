sensehatdetect = True;

if(sensehatdetect == False):
    print("SenseHat not detected")
else:
    # Connect to database
    import pymysql

    conn = pymysql.connect(
        host='localhost',
        user='root',
        password='',
        db='nerdygadgets',
        charset='utf8mb4'

    );

    # Generate a random number between 0 and -15
    import random
    temperature = random.randint(0, 15)

    # Get current datetime in ISO format
    import datetime
    now = datetime.datetime.now()

    try:
        with conn.cursor() as cursor:
            # Create a new record
            sql = "INSERT INTO `coldroomtemperatures_archive` (`ColdRoomSensorNumber`, `RecordedWhen`, `Temperature`, `ValidFrom`, `ValidTo`) VALUES (%s, %s, %s, %s, %s)"
            cursor.execute(sql, (5, now, temperature, now, now))

        # Commit changes
        conn.commit()

        print("Record inserted successfully")
    finally:
        conn.close()
