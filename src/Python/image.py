import cv2
import json
from json import JSONEncoder
import numpy
from argparse import ArgumentParser

parser = ArgumentParser()
parser.add_argument("-f", "--frame", dest="frame", metavar="FILE")

args = parser.parse_args()

img = cv2.imread(args.frame)
faces = cv2.CascadeClassifier('/var/www/public/images/faces.xml')
results = faces.detectMultiScale(img, scaleFactor=2, minNeighbors=3)

class NumpyArrayEncoder(JSONEncoder):
    def default(self, obj):
        if isinstance(obj, numpy.ndarray):
            return obj.tolist()
        return JSONEncoder.default(self, obj)

numpyData = {"array": results}
encodedNumpyData = json.dumps(numpyData, cls=NumpyArrayEncoder)  # use dump() to write array into file
print(encodedNumpyData)