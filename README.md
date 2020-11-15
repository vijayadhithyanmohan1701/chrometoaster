1. Gallery With Comments

A example of how the gallery paga type works. The admin uploads images and videos in the uploads directory. The gallery page type get the images using the has many relation with the Single Gallery image dataobject and retrieves images on the front end. Users can filter images based on year and event type.

Image opents to a lightbox gallery where users can post the comments by image. The admin has the privilleges to delete the comments or approve.

The comments approved are listed by image ID.

2. Members marked by location

Members are signed up and they are assigned a membership type. Members are listed in their dedicated pages and collectively as a list. Members are retrieved as dataobjects and listed on the front end using a map. 

The map assigns custom pins to each member and are dropped on the map based on their location and membership type (GMAPS is used to get locations from geocoding).

3. Products Specs with Case studies
Thsi is a page which is assigned to multiple categories and producs. Each case study can be tagged to multiple products using many to many relation and licenses are assigned to the product.


Each product is assigned to a specification or multiple specs. A specification page gets products, case studies, licenses linked etc and are listed on a template.

The is also a JS api used to list specifications on an infographic by getting the coordinates of a point in the infographi