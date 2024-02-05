import React, {useEffect} from 'react';
import { useFocusEffect } from '@react-navigation/native';
import { ActivityIndicator, KeyboardAvoidingView, BackHandler, SafeAreaView, StyleSheet, Text, View, TextInput, TouchableOpacity, ScrollView, Platform } from "react-native";
import { useToast } from "react-native-toast-notifications";
import Button from './button';
import './global.js';

export default function ManagePolls(props) {
    useFocusEffect(
        React.useCallback(() => {
            const onBackPress = () => {
                goback();
                return true;
            };

            BackHandler.addEventListener(
                'hardwareBackPress', onBackPress
            );

            return () =>
                BackHandler.removeEventListener(
                    'hardwareBackPress', onBackPress
                );
        }, [])
    );



    useEffect(() => {
        const requestOptions = {
            method: "GET",
            headers: {
                "LETSEAT-AUTHKEY": props.user.user_token,
                "Content-Type": "application/json"
            },
        };
          
        fetch(global.server + "poll/read.php?mine=1",requestOptions).then(res => res.json()).then(result => {
            setFeastGroups(result)
        }).then(() => {setLoading(false)})
          
    },[]);

    const styles = StyleSheet.create(
        Platform.select({
            ios: global.mainstyles,
            android: global.mainstyles,
            default: global.mainstyles
        })
    );


    const [Feasts,setFeasts] = React.useState([]);
    const [FeastGroups,setFeastGroups] = React.useState([]);
    const [initialLoading,setLoading] = React.useState(true);


    const [name, setName] = React.useState(props.user.name);
    const [notes, setNotes] = React.useState(props.user.name);
    const toast = useToast();
    const [isLoadingName, setLoadingName] = React.useState(false);

    const notesRef = React.useRef(null);


    const createPoll = () => {
        setLoadingName(true);
        if (name !== '' && name !== null && name !== undefined) {
            const requestOptions = {
                method: "POST",
                headers: {
                    "LETSEAT-AUTHKEY": props.user.user_token,
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    name:name,
                    notes:notes
                }),
            };

            fetch(global.server + "poll/create.php", requestOptions).then(res => res.json()).then(result => {
                // console.log(result);
                if (result.password) {
                    alert("Poll Created Successfully!");
                } else {
                    alert("Poll Not Created");
                }
            }).catch(() => {
                setTimeout(() => {
                    setLoadingName(false);
                    alert("Failed To Connect To Server");
                }, 2000);
            }).finally(() => {
                setTimeout(() => setLoadingName(false), 2000);
            });
        } else {
            toast.show("Please enter text into the field", {
                type: "warning",
                placement: "top",
                duration: 4000,
                offset: 100,
                animationType: "slide-in",
            });
            setLoadingName(false);
        }
    }

    const goback = () => {
        props.pagging(1)
    }

    const goToAdd = () => {
        props.pagging(7)
    }

    return (
        <SafeAreaView>
            <View style={styles.main}>
                <View style={styles.navbar}>
                    <View style={styles.backbuttonview}>
                        <Button
                            textstyle={styles.backbutton}
                            title="Back"
                            onPress={goback}>
                        </Button>
                    </View>
                    <View style={styles.addbuttonview}>
                        <Button
                            textstyle={styles.addbutton}
                            title="Add"
                            onPress={goToAdd}>
                        </Button>
                    </View>
                </View>
                <View style={styles.scrollView}>
                    <KeyboardAvoidingView
                        behavior={Platform.OS === "ios" ? "height" : "padding"}
                        enabled>
                        <ScrollView>
                            <View
                                style={styles.signin}
                            >
                                <Text style={styles.dashboardheader}>
                                    Poll Manager
                                </Text>
                                <Text>Groups</Text>
                                {
                                    initialLoading?
                                    <View key={Math.random(50000)} style={styles.topbutton}>
                                        <ActivityIndicator key={Math.random(50000)} color={styles.topbutton.color} size="large" />
                                    </View>:
                                    FeastGroups.map(group => {
                                        return(<Button 
                                            key={Math.random(50000)}
                                            onPress={() => {
                                                props.setPoll(group.id);
                                                props.pagging(15);
                                            }}
                                            title={group.name}
                                            // textstyle={styles.button}
                                            textstyle={styles.topbutton}
                                            accessibilityLabel={group.name}
                                            isLoading={isLoadingName}
                                        ></Button>)
                                    })
                            }
                            </View>
                        </ScrollView>
                    </KeyboardAvoidingView>
                </View>
            </View>
        </SafeAreaView>

    )
}