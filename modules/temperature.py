sensehatdetect = True;
coldroomtemeratureNumber = 5



while(True) :
    # Get current datetime in ISO format
    import datetime
    now = datetime.datetime.now()

    # Generate a random number between 0 and 15
    import random
    temperature = random.randint(0, 15)

    # Connect to database
    import pymysql

    conn = pymysql.connect(
        host='localhost',
        user='root',
        password='',
        db='nerdygadgets',
        charset='utf8mb4'

    );

    try:
        with conn.cursor() as cursor:
            # Create a new record
            sql = "INSERT INTO `coldroomtemperatures_archive` (`ColdRoomSensorNumber`, `RecordedWhen`, `Temperature`, `ValidFrom`, `ValidTo`) VALUES (%s, %s, %s, %s, %s)"
            cursor.execute(sql, (coldroomtemeratureNumber, now, temperature, now, '9999-12-31 23:59:59'))

            # Delete old coldroomtemeratures record
            sql = "DELETE FROM `coldroomtemperatures` WHERE `ColdRoomSensorNumber`=%s"
            cursor.execute(sql, (coldroomtemeratureNumber))

            # Add new coldroomtemeratures record
            sql = "INSERT INTO `coldroomtemperatures` (`ColdRoomSensorNumber`, `RecordedWhen`, `Temperature`, `ValidFrom`, `ValidTo`) VALUES (%s, %s, %s, %s, %s)"
            cursor.execute(sql, (coldroomtemeratureNumber, now, temperature, now, now))

        # Commit changes
        conn.commit()

        print("Record inserted successfully")
    finally:
        conn.close()

    # Wait 5 seconds
    import time
    time.sleep(3)