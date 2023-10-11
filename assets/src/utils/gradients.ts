import {
    blue as blueColor,
    green as greenColor,
    grey as greyColor,
    indigo as indigoColor,
    red as redColor,
} from '@mui/material/colors';
import { primaryMain, secondaryMain } from '../theme/palette';

const buildGradient = (start: string, end: string, degree?: number) =>
    `linear-gradient(${degree || 45}deg, ${start}, ${end})`;

const grey = buildGradient(greyColor[400], greyColor[600]);
const blue = buildGradient(blueColor[700], blueColor[900]);
const indigo = buildGradient(indigoColor[400], indigoColor[600]);
const green = buildGradient(greenColor[400], greenColor[600]);
const orange = buildGradient('#FF5200', '#FF9E00');
const red = buildGradient(redColor[500], redColor[700]);
const primary = buildGradient(primaryMain, secondaryMain, 90);

export default {
    grey,
    blue,
    indigo,
    green,
    orange,
    red,
    primary,
    buildGradient,
};
