# Samaaja Geolocation Engine
Converts any lat-long to admin/political geoboundaries in India easily. 

## Who this is for (Target users)
- Any one who wants to self host and/or convert lat-long information to geoboundaries
- Anyone who wants to contribute to a larger system to allow any stakeholder (Citizen/Govt/NGO/Academician/Media/Reserachers) to enrich lat-long data

*PS - please see Dictionary Section below if you don't understand some abbreviations*

## Description and Motivation
While working on rapid-response data (Ex: during COVID), we found a lot of requests for help coming via WhatsApp. Users usually shared either current locations with Lat-Longs or google map links. While data collection was easy using WhatsApp, there was no system existing to enrich Lat-Longs embeded in the WhatsApp Current Location or in google map links to *Address, postalcode, city, Wards, Assembly Constituency, LokSabha Constitutency, Gram Panchayath etc*. 

In terms of data to use, there was also no one single repository of all types of political and administrative goeboundaries for the country of India. Since multiple stakeholders (Govt Depts, NGOs, Citizens) creted KMLs for different regions, there was no single standard followed in naming conventions. 

So we decided to build this system as a **Digital Public Good** and open source the code for anyone to contribute to or self host for non-commercial purposes. The features are listed below

## Technologies
- Database: *Postgres* stores KML files
- DB Plugin: *PostGIS* queries across multiple goeboundaries and delivers results back as a JSON
- Geoconversion Code written in *Laravel*
- Popup map code written in *JS*

### Features
#### I. Admin section (Data Ingestion)
1. Map Key-Value pairs (KVPs) so that the parser can extract and map different names for similar types of geoboundaries. Ex: We have noticed a ward is called KGISWard_Name in Karnataka, WardName in Chennai, Ward_Name by a CSO contributor called DataMeeet. This feature allows you to map KVPs once so the system can automatically extract information in the future.
!(KVP screen)[image.jpeg] shows you how to load data.
2. Ingestion feature - accepts only KML files less than 100 MB as of now. To convert GeoJSON or Shape files, refer to the section on converting file formats below. !(Ingestion screen)[image.jpeg] shows different options in the dropdown. Please refer to the guide on How to ingest data below for detailed instructions

### II. Geo-conversion Logic
1. **Google Maps API** is used to convert lat-long to address, postalcode, city, locality. This allows this system to be used globally, and in areas without other KML files.
2. **RB Locations system** is used to convert the same Lat-Long into Ward, GP, AC, LS etc

### Data Processing
1. Bulk process via a python script -[GitRepo here](https://github.com/reapbenefit/offlinegoecoder). You can use this for bulk processing on your localhost.
2. Use cURL via command prompt for 1 off conversions or use the [Geo Converter Website](https://reapbenefit.github.io/geolocationwebsite/)
3. Integrate into the Glific WhatsApp Chatbot ecosystem using this code.

### Enriching and Delivery via [Glific's](http://www.glific.com) WhatsApp Chatbot
If you are using a Whatsapp Chatbot powered by [Glific](http://www.glific.com) , this system inserts all the parameters retrieved into the user's contact profile.

We invite support in the form of:
- further development
- contribution to the KML files

## Dictionary
Ward : 


## Running instructions
1. Install Laravel if not done

### Demo section


### How to create custom KML files


### How to convert file formats
Load the geo file into Google Earth Pro (Thank you Google Earth team). Save the loaded geoboundary as a KML, and not as a KMZ


### How to ingest data


### Standards suggested for Naming Conventions


## Data Ingested
### Karnataka State
1. All AC boundaries
2. All LS boundaries
3. GP boundaries
4. Village boundaries
5. Hobli boundaries


## Credits
### Development
ColoredCow team for contributing to the code
Donald Lobo for guidance and being sounding board for the vision
Reap Benefit team for identifying the need, creating requirements and working on the system and Geodata loading, testing and visioning

### Data
GID
State GIS websites (esp Karnataka. Special thanks to the Dept of Women and Child Development.)
